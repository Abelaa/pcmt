<?php

/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtDraftBundle\Tests\Datasource;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Query\ProductQueryBuilderFactoryInterface;
use Akeneo\Tool\Component\StorageUtils\Exception\InvalidObjectException;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\PimDataGridBundle\EventSubscriber\FilterEntityWithValuesSubscriber;
use Oro\Bundle\PimDataGridBundle\Extension\Pager\PagerExtension;
use PcmtDraftBundle\Datasource\AssociatedProductDatasource;
use PcmtDraftBundle\Entity\AbstractDraft;
use PcmtDraftBundle\Repository\DraftRepository;
use PcmtDraftBundle\Service\Draft\GeneralObjectFromDraftCreator;
use PcmtDraftBundle\Tests\TestDataBuilder\DatagridBuilder;
use PcmtDraftBundle\Tests\TestDataBuilder\ExistingProductDraftBuilder;
use PcmtDraftBundle\Tests\TestDataBuilder\ProductBuilder;
use PcmtDraftBundle\Tests\TestDataBuilder\ProductModelBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AssociatedProductDatasourceTest extends TestCase
{
    /** @var AssociatedProductDatasource */
    private $associatedProductDatasource;

    /** @var ObjectManager|MockObject */
    private $objectManagerMock;

    /** @var ProductQueryBuilderFactoryInterface|MockObject */
    private $productQueryBuilderFactoryMock;

    /** @var NormalizerInterface|MockObject */
    private $serializerMock;

    /** @var FilterEntityWithValuesSubscriber|MockObject */
    private $filterEntityWithValuesSubscriberMock;

    /** @var GeneralObjectFromDraftCreator|MockObject */
    private $generalObjectFromDraftCreatorMock;

    /** @var DraftRepository|MockObject */
    private $draftRepositoryMock;

    protected function setUp(): void
    {
        $this->objectManagerMock = $this->createMock(ObjectManager::class);
        $this->productQueryBuilderFactoryMock = $this->createMock(
            ProductQueryBuilderFactoryInterface::class
        );
        $this->serializerMock = $this->createMock(NormalizerInterface::class);
        $this->filterEntityWithValuesSubscriberMock = $this->createMock(
            FilterEntityWithValuesSubscriber::class
        );
        $this->generalObjectFromDraftCreatorMock = $this->createMock(
            GeneralObjectFromDraftCreator::class
        );
        $this->draftRepositoryMock = $this->createMock(DraftRepository::class);

        $this->associatedProductDatasource = new AssociatedProductDatasource(
            $this->objectManagerMock,
            $this->productQueryBuilderFactoryMock,
            $this->serializerMock,
            $this->filterEntityWithValuesSubscriberMock
        );

        $this->associatedProductDatasource->setCreator($this->generalObjectFromDraftCreatorMock);
        $this->associatedProductDatasource->setDraftRepository($this->draftRepositoryMock);
    }

    public function dataGetResults(): array
    {
        return [
            'creator_returns_base_product' => [
                'baseProduct' => (new ProductBuilder())->build(),
            ],
            'creator_returns_null'         => [
                'baseProduct' => null,
            ],
        ];
    }

    /**
     * @dataProvider dataGetResults
     */
    public function testGetResults(?ProductInterface $baseProduct): void
    {
        $this->draftRepositoryMock
            ->method('findOneBy')
            ->with(
                [
                    'status'  => AbstractDraft::STATUS_NEW,
                    'product' => (new ProductBuilder())->build()->getId(),
                ]
            )
            ->willReturn((new ExistingProductDraftBuilder())->build());

        $this->generalObjectFromDraftCreatorMock
            ->method('getObjectToSave')
            ->willReturn($baseProduct);

        $this->associatedProductDatasource->process(
            (new DatagridBuilder())->build(),
            [
                'locale_code'                  => 'test',
                'scope_code'                   => 'test',
                PagerExtension::PER_PAGE_PARAM => 25,
                'current_product'              => (new ProductBuilder())->build(),
                'association_type_id'          => 1,
            ]
        );
        $results = $this->associatedProductDatasource->getResults();

        $this->assertEquals(
            [
                'totalRecords' => 0,
                'data'         => [],
            ],
            $results
        );
    }

    public function testGetResultsWhenSourceProductIsNotFound(): void
    {
        $this->draftRepositoryMock->method('findOneBy')->with(
            [
                'status'  => AbstractDraft::STATUS_NEW,
                'product' => (new ProductBuilder())->build()->getId(),
            ]
        )->willReturn((new ExistingProductDraftBuilder())->build());

        $this->expectException(InvalidObjectException::class);

        $this->associatedProductDatasource->process(
            (new DatagridBuilder())->build(),
            [
                'locale_code'                  => 'test',
                'scope_code'                   => 'test',
                PagerExtension::PER_PAGE_PARAM => 25,
                'current_product'              => null,
            ]
        );
        $this->associatedProductDatasource->getResults();
    }

    public function testGetResultWhenSourceProductIsNotInstanceOfProductInterface(): void
    {
        $this->draftRepositoryMock->method('findOneBy')->with(
            [
                'status'  => AbstractDraft::STATUS_NEW,
                'product' => (new ProductBuilder())->build()->getId(),
            ]
        )->willReturn((new ExistingProductDraftBuilder())->build());

        $this->expectException(InvalidObjectException::class);

        $this->associatedProductDatasource->process(
            (new DatagridBuilder())->build(),
            [
                'locale_code'                  => 'test',
                'scope_code'                   => 'test',
                PagerExtension::PER_PAGE_PARAM => 25,
                'current_product'              => (new ProductModelBuilder())->build(),
            ]
        );
        $this->associatedProductDatasource->getResults();
    }

    public function testGetResultWhenRepositoryDidNotReturnInstanceOfDraftInterface(): void
    {
        $this->draftRepositoryMock->method('findOneBy')->with(
            [
                'status'  => AbstractDraft::STATUS_NEW,
                'product' => (new ProductBuilder())->build()->getId(),
            ]
        )->willReturn((new ProductModelBuilder())->build());

        $this->expectException(InvalidObjectException::class);

        $this->associatedProductDatasource->process(
            (new DatagridBuilder())->build(),
            [
                'locale_code'                  => 'test',
                'scope_code'                   => 'test',
                PagerExtension::PER_PAGE_PARAM => 25,
                'current_product'              => (new ProductBuilder())->build(),
            ]
        );
        $this->associatedProductDatasource->getResults();
    }
}