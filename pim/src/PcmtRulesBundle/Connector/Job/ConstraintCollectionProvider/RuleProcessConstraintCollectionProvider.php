<?php
/**
 * Copyright (c) 2020, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtRulesBundle\Connector\Job\ConstraintCollectionProvider;

use Akeneo\Tool\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use PcmtCoreBundle\Connector\Job\JobParameters\SupportedJobsTrait;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class RuleProcessConstraintCollectionProvider implements ConstraintCollectionProviderInterface
{
    use SupportedJobsTrait;

    public function __construct(array $supportedJobNames)
    {
        $this->supportedJobNames = $supportedJobNames;
    }

    public function getConstraintCollection(): Collection
    {
        return new Collection([
            'fields' => [
                'ruleId'  => [
                    new NotBlank(),
                    new Type('int'),
                ],
            ],
        ]);
    }
}