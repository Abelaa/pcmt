<?php
/**
 * Copyright (c) 2019, VillageReach
 * Licensed under the Non-Profit Open Software License version 3.0.
 * SPDX-License-Identifier: NPOSL-3.0
 */

declare(strict_types=1);

namespace PcmtCoreBundle\JobParameters\DefaultValueProvider;

use Akeneo\Tool\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use PcmtCoreBundle\JobParameters\SupportedJobsTrait;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class XmlReferenceDataImport implements DefaultValuesProviderInterface
{
    use SupportedJobsTrait;

    /** @var string */
    protected $fileDirectory = 'Xml';

    public function __construct(
        array $supportedJobNames
    ) {
        $this->supportedJobNames = $supportedJobNames;
    }

    public function getDefaultValues(): array
    {
        return [
            'filePath'      => null,
            'dirPath'       => 'src/PcmtCoreBundle/Resources/reference_data/gs1Codes/',
            'uploadAllowed' => [
                new Type('bool'),
                new IsTrue(['groups' => 'UploadExecution']),
            ],
            'decimalSeparator' => new NotBlank(),
            'xmlMapping'       => [
                '{http://www.w3.org/2001/XMLSchema-instance}urn' => 'Sabre\Xml\Element\XmlElement',
                'code'                                           => 'Sabre\Xml\Element\KeyValue',
            ],
        ];
    }
}