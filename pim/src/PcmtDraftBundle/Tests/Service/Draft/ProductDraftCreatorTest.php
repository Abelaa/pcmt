<?php

/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtDraftBundle\Tests\Service\Draft;

use PcmtDraftBundle\Entity\ExistingProductDraft;
use PcmtDraftBundle\Entity\NewProductDraft;
use PcmtDraftBundle\Service\Draft\ProductDraftCreator;
use PcmtDraftBundle\Tests\TestDataBuilder\ProductBuilder;
use PcmtDraftBundle\Tests\TestDataBuilder\UserBuilder;
use PHPUnit\Framework\TestCase;

class ProductDraftCreatorTest extends TestCase
{
    /** @var ProductDraftCreator */
    private $productDraftCreator;

    protected function setUp(): void
    {
        $this->productDraftCreator = new ProductDraftCreator();
    }

    public function testCreateWhenProductExists(): void
    {
        $product = (new ProductBuilder())
            ->build();

        $user = (new UserBuilder())
            ->build();

        $draft = $this->productDraftCreator->create(
            $product,
            ['ATTRIBUTE' => 'VALUE'],
            $user
        );

        $this->assertInstanceOf(ExistingProductDraft::class, $draft);
        $this->assertEquals(['ATTRIBUTE' => 'VALUE'], $draft->getProductData());
    }

    public function testCreateWhenProductDoesNotExist(): void
    {
        $product = (new ProductBuilder())
            ->withId(null)
            ->build();

        $user = (new UserBuilder())
            ->build();

        $draft = $this->productDraftCreator->create(
            $product,
            ['ATTRIBUTE' => 'VALUE'],
            $user
        );

        $this->assertInstanceOf(NewProductDraft::class, $draft);
        $this->assertEquals(['ATTRIBUTE' => 'VALUE'], $draft->getProductData());
    }
}