<?php

declare(strict_types=1);

namespace Pcmt\PcmtConnectorBundle\Command\Handler;

class GS1CodesDownloadHandler extends PcmtReferenceDataDownloadHandler
{
    public function __construct($name = null)
    {
        parent::__construct($name);
    }
}