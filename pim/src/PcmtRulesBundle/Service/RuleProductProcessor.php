<?php

declare(strict_types=1);

/*
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

namespace PcmtRulesBundle\Service;

use Akeneo\Channel\Component\Repository\ChannelRepositoryInterface;
use Akeneo\Channel\Component\Repository\LocaleRepositoryInterface;
use Akeneo\Pim\Enrichment\Bundle\Elasticsearch\ProductQueryBuilderFactory;
use Akeneo\Pim\Enrichment\Component\Product\Builder\ProductBuilderInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\EntityWithValuesInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\Filter\Operators;
use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Akeneo\Tool\Component\Batch\Model\StepExecution;
use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Tool\Component\StorageUtils\Updater\PropertyCopierInterface;
use PcmtCoreBundle\Connector\Job\InvalidItems\SimpleInvalidItem;
use PcmtRulesBundle\Entity\Rule;
use Ramsey\Uuid\Uuid;

class RuleProductProcessor
{
    /** @var ProductQueryBuilderFactory */
    private $pqbFactory;

    /** @var RuleAttributeProvider */
    private $ruleAttributeProvider;

    /** @var PropertyCopierInterface */
    private $propertyCopier;

    /** @var SaverInterface */
    private $productSaver;

    /** @var SaverInterface */
    private $productModelSaver;

    /** @var ProductBuilderInterface */
    private $productBuilder;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var LocaleRepositoryInterface */
    private $localeRepository;

    /** @var ProductInterface[] */
    private $productsToSave = [];

    /** @var ProductModelInterface[] */
    private $productModelsToSave = [];

    public function __construct(
        ProductQueryBuilderFactory $pqbFactory,
        RuleAttributeProvider $ruleAttributeProvider,
        PropertyCopierInterface $propertyCopier,
        SaverInterface $productSaver,
        SaverInterface $productModelSaver,
        ProductBuilderInterface $productBuilder,
        ChannelRepositoryInterface $channelRepository,
        LocaleRepositoryInterface $localeRepository
    ) {
        $this->pqbFactory = $pqbFactory;
        $this->ruleAttributeProvider = $ruleAttributeProvider;
        $this->propertyCopier = $propertyCopier;
        $this->productSaver = $productSaver;
        $this->productModelSaver = $productModelSaver;
        $this->productBuilder = $productBuilder;
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;
    }

    public function process(StepExecution $stepExecution, Rule $rule, EntityWithValuesInterface $sourceProduct): void
    {
        $this->productsToSave = [];
        $this->productModelsToSave = [];

        $attributes = $this->ruleAttributeProvider->getForFamilies($rule->getSourceFamily(), $rule->getDestinationFamily());

        $keyValue = $sourceProduct->getValue($rule->getKeyAttribute()->getCode());
        if (!$keyValue) {
            return;
        }

        // searching through ElasticSearch index
        $pqb = $this->pqbFactory->create([
            'default_locale' => null,
            'default_scope'  => null,
        ]);
        try {
            $pqb->addFilter($rule->getKeyAttribute()->getCode(), Operators::EQUALS, $keyValue->getData());
        } catch (\Throwable $e) {
            try {
                $pqb->addFilter($rule->getKeyAttribute()->getCode(), Operators::IN_LIST, [$keyValue->getData()]);
            } catch (\Throwable $e) {
                throw new \Exception('Unsupported attribute type used as key attribute in a rule: ' . $rule->getKeyAttribute()->getCode());
            }
        }
        $pqb->addFilter('family', Operators::IN_LIST, [$rule->getDestinationFamily()->getCode()]);

        $destinationProducts = $pqb->execute();
        foreach ($destinationProducts as $destinationProduct) {
            foreach ($attributes as $attribute) {
                /** @var AttributeInterface $attribute */
                try {
                    $this->copyData($sourceProduct, $destinationProduct, $attribute);
                } catch (\Throwable $e) {
                    $msg = sprintf(
                        'Problem with copying data from product %s to product %s, attribute: %s. Error: %s',
                        $sourceProduct->getId(),
                        $destinationProduct->getId(),
                        $attribute->getLabel(),
                        $e->getMessage()
                    );
                    $invalidItem = new SimpleInvalidItem(
                        [
                            'sourceProduct'      => $sourceProduct->getIdentifier(),
                            'destinationProduct' => $destinationProduct->getIdentifier(),
                            'attribute'          => $attribute->getCode(),
                        ]
                    );
                    $stepExecution->addWarning($msg, [], $invalidItem);
                }
            }
        }

        if (0 === count($destinationProducts) && 0 === count($rule->getDestinationFamily()->getFamilyVariants())) {
            $this->createNewDestinationProduct($sourceProduct, $rule, $attributes);
            $stepExecution->incrementSummaryInfo('destination_products_created', 1);
        }

        foreach ($this->productsToSave as $product) {
            $this->productSaver->save($product);
            $stepExecution->incrementSummaryInfo('destination_products_found_and_saved', 1);
        }
        foreach ($this->productModelsToSave as $productModel) {
            $this->productModelSaver->save($productModel);
            $stepExecution->incrementSummaryInfo('destination_product_models_found_and_saved', 1);
        }
    }

    private function createNewDestinationProduct(ProductInterface $sourceProduct, Rule $rule, array $attributes): void
    {
        $destinationProduct = $this->productBuilder->createProduct(
            Uuid::uuid4()->toString(),
            $rule->getDestinationFamily()->getCode()
        );
        foreach ($attributes as $attribute) {
            /** @var AttributeInterface $attribute */
            $this->copyData($sourceProduct, $destinationProduct, $attribute);
        }

        $this->productsToSave[] = $destinationProduct;
    }

    private function copyData(EntityWithValuesInterface $sourceProduct, EntityWithValuesInterface $destinationProduct, AttributeInterface $attribute): void
    {
        if ($destinationProduct instanceof ProductInterface) {
            $this->productsToSave[$destinationProduct->getId()] = $destinationProduct;
        } elseif ($destinationProduct instanceof ProductModelInterface) {
            $this->productModelsToSave[$destinationProduct->getId()] = $destinationProduct;
        }

        $scopes = $attribute->isScopable() ? $this->channelRepository->getChannelCodes() : null;
        $locales = $attribute->isLocalizable() ? $this->localeRepository->getActivatedLocaleCodes() : null;

        $scopes = $scopes ?? [null];
        $locales = $locales ?? [null];

        foreach ($locales as $localeCode) {
            foreach ($scopes as $scopeCode) {
                $options = [
                    'from_locale' => $localeCode,
                    'to_locale'   => $localeCode,
                    'from_scope'  => $scopeCode,
                    'to_scope'    => $scopeCode,
                ];
                $this->propertyCopier->copyData(
                    $sourceProduct,
                    $destinationProduct,
                    $attribute->getCode(),
                    $attribute->getCode(),
                    $options
                );
            }
        }

        if ($destinationProduct->getParent()) {
            $this->copyData($sourceProduct, $destinationProduct->getParent(), $attribute);
        }
    }
}