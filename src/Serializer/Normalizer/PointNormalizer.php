<?php

namespace App\Serializer\Normalizer;

use LongitudeOne\Spatial\PHP\Types\AbstractPoint;

class PointNormalizer extends AbstractCacheableNormalizer
{
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof AbstractPoint;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        return [
            'lat'  => $object->getLatitude(),
            'long' => $object->getLongitude()
        ];
    }
}
