<?php
/**
 * Copyright (c) 2019, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtCoreBundle\Normalizer\Standard;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ConcatenatedTypeNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($cncAttribute, $format = null, array $context = []): array
    {
        $attributes = $cncAttribute->getProperty('attributes');
        $separators = $cncAttribute->getProperty('separators');

        $output = [];
        if ($attributes) {
            $serialized = explode(',', $attributes);
            for ($i = 1; $i <= count($serialized); $i++) {
                $prefix = 'attribute' . (string) $i;
                $output[$prefix] = $serialized[$i - 1];
            }
        }

        if ($separators) {
            $output['separator1'] = $separators;
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AttributeInterface;
    }
}