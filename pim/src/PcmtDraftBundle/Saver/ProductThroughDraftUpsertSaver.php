<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

namespace PcmtDraftBundle\Saver;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use PcmtDraftBundle\Entity\AbstractDraft;
use PcmtDraftBundle\Entity\ExistingProductDraft;
use PcmtDraftBundle\Exception\DraftWithNoChangesException;
use PcmtDraftBundle\Repository\DraftRepository;
use PcmtDraftBundle\Service\Draft\BaseEntityCreatorInterface;
use PcmtDraftBundle\Service\Draft\DraftCreatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductThroughDraftUpsertSaver implements SaverInterface
{
    /** @var SaverInterface */
    private $entitySaver;

    /** @var NormalizerInterface */
    private $standardNormalizer;

    /** @var SaverInterface */
    private $draftSaver;

    /** @var BaseEntityCreatorInterface */
    private $baseEntityCreator;

    /** @var DraftCreatorInterface */
    private $draftCreator;

    /** @var DraftRepository */
    private $draftRepository;

    public function __construct(
        SaverInterface $entitySaver,
        NormalizerInterface $standardNormalizer,
        SaverInterface $draftSaver,
        BaseEntityCreatorInterface $baseEntityCreator,
        DraftCreatorInterface $draftCreator,
        DraftRepository $draftRepository
    ) {
        $this->entitySaver = $entitySaver;
        $this->standardNormalizer = $standardNormalizer;
        $this->draftSaver = $draftSaver;
        $this->baseEntityCreator = $baseEntityCreator;
        $this->draftCreator = $draftCreator;
        $this->draftRepository = $draftRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function save($object, array $options = []): void
    {
        if (!$object instanceof ProductInterface) {
            throw new \InvalidArgumentException('Wrong object class in '. self::class.': '. get_class($object));
        }
        /** @var ProductInterface $object */
        // following is needed, as normalizer will throw an exception otherwise
        $object->setCreated(new \DateTime());
        $object->setUpdated(new \DateTime());
        $baseObject = $this->getEntityOrCreateIfNotExists($object);
        if (!$object->getId() && $baseObject->getId()) {
            $object->setId($baseObject->getId());
        }
        $data = $this->standardNormalizer->normalize($object, 'standard', ['import_via_drafts']);

        $criteria = [
            'status'  => AbstractDraft::STATUS_NEW,
            'product' => $baseObject,
        ];

        /** @var ExistingProductDraft $draft */
        $draft = $this->draftRepository->findOneBy($criteria);
        if (!$draft) {
            $draft = $this->draftCreator->create($baseObject, $data);
        } else {
            $draft->setProductData($data);
        }

        try {
            $this->draftSaver->save($draft, [
                DraftSaver::OPTION_NO_VALIDATION           => true,
                DraftSaver::OPTION_DONT_SAVE_IF_NO_CHANGES => true,
            ]);
        } catch (DraftWithNoChangesException $e) {
        }
    }

    private function getEntityOrCreateIfNotExists(ProductInterface $object): ProductInterface
    {
        if ($object->getId()) {
            return $object;
        }

        $baseEntity = $this->baseEntityCreator->create($object);

        $this->entitySaver->save($baseEntity);

        return $baseEntity;
    }
}