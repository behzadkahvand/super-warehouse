<?php

namespace App\Service\Integration;

use App\Annotations\Integrationable;
use App\Service\Annotation\AnnotationDiscoverService;
use Laminas\Code\Reflection\PropertyReflection;

class IntegrationablePropertiesDiscoverService
{
    public function __construct(private AnnotationDiscoverService $annotationDiscoverService)
    {
    }

    public function getIntegrationableProperties(string $class, array $groups): array
    {
        $integrationableProperties = [];

        /** @var Integrationable $annotation */
        /** @var PropertyReflection $property */
        foreach (
            $this->annotationDiscoverService->getClassPropertiesAnnotation(
                $class,
                Integrationable::class
            ) as $property => $annotation
        ) {
            $diffGroup = array_diff($groups, $annotation->groups);

            if (!isset($integrationableProperties[$property->getName()]) && (count($diffGroup) < count($groups))) {
                $integrationableProperties[] = $property->getName();
            }
        }

        return $integrationableProperties;
    }
}
