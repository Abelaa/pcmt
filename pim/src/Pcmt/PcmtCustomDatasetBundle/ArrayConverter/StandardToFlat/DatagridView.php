<?php
declare(strict_types=1);

namespace Pcmt\PcmtCustomDatasetBundle\ArrayConverter\StandardToFlat;

use Akeneo\Tool\Component\Connector\ArrayConverter\ArrayConverterInterface;
use Akeneo\Tool\Component\Connector\ArrayConverter\StandardToFlat\AbstractSimpleArrayConverter;

/**
 * Convert standard format to flat format for category
 *
 * @author    Adrien Pétremann <adrien.petremann@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class DatagridView extends AbstractSimpleArrayConverter implements ArrayConverterInterface
{
  /**
   * {@inheritdoc}
   */
  protected function convertProperty($property, $data, array $convertedItem, array $options): array
  {
    switch ($property) {
      case 'labels':
        foreach ($data as $localeCode => $label) {
          $labelKey = sprintf('label-%s', $localeCode);
          $convertedItem[$labelKey] = $label;
        }
        break;
      default:
        $convertedItem[$property] = (string) $data;
    }

    return $convertedItem;
  }
}