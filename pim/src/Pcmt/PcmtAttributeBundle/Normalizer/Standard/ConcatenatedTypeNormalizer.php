<?php
declare(strict_types=1);

namespace Pcmt\PcmtAttributeBundle\Normalizer\Standard;

use Akeneo\Pim\Structure\Component\Model\AttributeInterface;
use Pcmt\PcmtAttributeBundle\Extension\ConcatenatedAttribute\Structure\Component\AttributeType\PcmtAtributeTypes;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ConcatenatedTypeNormalizer implements NormalizerInterface
{
    public function normalize($cncAttribute, $format = null, array $context = []): array
    {
        if(!empty($context)){
            throw new \InvalidArgumentException('Concatenated attributes are out of any scope.');
        }

        $attributes = $cncAttribute->getProperty('attributes');
        $separators = $cncAttribute->getProperty('separators');

        $output = [];
        if($attributes){
            $serialized = explode(',', $attributes);
            for($i = 1; $i <= count($serialized); $i++){
                $prefix = 'attribute' . (string)$i;
                $output[$prefix] = $serialized[$i - 1];
            }
        }

        if($separators){
            $serialized = explode(',', $separators);
            for($i = 1; $i <= count($serialized); $i++){
                $prefix = 'separator' . (string)$i;
                $output[$prefix] = $serialized[$i - 1];
            }
        }

        return $output;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AttributeInterface;
    }
}