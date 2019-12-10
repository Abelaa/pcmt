<?php

declare(strict_types=1);

namespace PcmtCoreBundle\Provider\Form;

use Akeneo\Platform\Bundle\UIBundle\Provider\Form\FormProviderInterface;
use PcmtCoreBundle\Entity\DraftInterface;

class ProductModelDraftFormProvider implements FormProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getForm($element): string
    {
        return 'pcmt-product-model-drafts-edit';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($element): bool
    {
        return $element instanceof DraftInterface;
    }
}