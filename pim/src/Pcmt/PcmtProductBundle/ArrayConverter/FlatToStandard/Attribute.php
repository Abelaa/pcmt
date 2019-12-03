<?php

declare(strict_types=1);

namespace Pcmt\PcmtProductBundle\ArrayConverter\FlatToStandard;

use Akeneo\Pim\Structure\Component\ArrayConverter\FlatToStandard\Attribute as BaseAttribute;

/**
 * @override: Handle localizable attribute description in flat array to standard array conversion
 */
class Attribute extends BaseAttribute
{
    /**
     * {@inheritdoc}
     *
     * Converts flat csv array to standard structured array:
     */
    public function convert(array $item, array $options = [])
    {
        $this->fieldChecker->checkFieldsPresence($item, ['code']);
        $this->fieldChecker->checkFieldsFilling($item, ['code']);

        $convertedItem = [
            'labels'       => [],
            'descriptions' => [],
        ]; // add descriptions field to convertedItem array

        foreach ($item as $field => $data) {
            $convertedItem = $this->convertFields($field, $this->booleanFields, $data, $convertedItem);
        }

        return $convertedItem;
    }

    /**
     * {@inheritdoc}
     */
    protected function convertFields($field, $booleanFields, $data, $convertedItem): array
    {
        if (false !== mb_strpos($field, 'description-', 0)) {
            $descriptionTokens = explode('-', $field);
            $descriptionLocale = $descriptionTokens[1];
            $convertedItem['descriptions'][$descriptionLocale] = $data; // convert all localizable values of  attribute description field
        } else {
            $convertedItem = parent::convertFields($field, $booleanFields, $data, $convertedItem);
        }

        return $convertedItem;
    }
}
