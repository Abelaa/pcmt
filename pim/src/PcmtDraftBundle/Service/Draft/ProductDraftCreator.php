<?php

/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtDraftBundle\Service\Draft;

use Akeneo\UserManagement\Component\Model\UserInterface;
use PcmtDraftBundle\Entity\AbstractDraft;
use PcmtDraftBundle\Entity\ExistingProductDraft;
use PcmtDraftBundle\Entity\NewProductDraft;

class ProductDraftCreator implements DraftCreatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(
        $baseEntity,
        array $productData,
        ?UserInterface $author = null
    ): AbstractDraft {
        if ($baseEntity->getId()) {
            return new ExistingProductDraft(
                $baseEntity,
                $productData,
                new \DateTime(),
                $author
            );
        }

        return new NewProductDraft(
            $productData,
            new \DateTime(),
            $author
        );
    }
}