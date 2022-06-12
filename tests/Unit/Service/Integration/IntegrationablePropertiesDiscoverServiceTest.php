<?php

namespace App\Tests\Unit\Service\Integration;

use App\Annotations\Integrationable;
use App\Entity\Inventory;
use App\Service\Annotation\AnnotationDiscoverService;
use App\Service\Integration\IntegrationablePropertiesDiscoverService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use ReflectionProperty;

class IntegrationablePropertiesDiscoverServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|AnnotationDiscoverService|Mockery\MockInterface|null $annotationDiscoverService;

    protected IntegrationablePropertiesDiscoverService|null $integrationablePropertiesDiscoverService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->annotationDiscoverService                = Mockery::mock(AnnotationDiscoverService::class);
        $this->integrationablePropertiesDiscoverService = new IntegrationablePropertiesDiscoverService($this->annotationDiscoverService);
    }

    public function testGetIntegrationablePropertiesSuccess(): void
    {
        $class  = Inventory::class;
        $groups = ["test1"];

        $this->annotationDiscoverService->shouldReceive("getClassPropertiesAnnotation")
                                        ->once()
                                        ->with($class, Integrationable::class)
                                        ->andReturn($this->getResultClassProperty());

        $result = $this->integrationablePropertiesDiscoverService->getIntegrationableProperties($class, $groups);

        self::assertEquals(1, count($result));
        self::assertEquals("price", $result[0]);
    }

    protected function getResultClassProperty(): iterable
    {
        $property1 = Mockery::mock(ReflectionProperty::class);
        $property1->shouldReceive("getName")
                  ->twice()
                  ->withNoArgs()
                  ->andReturn("price");

        $annotation1 = new Integrationable(['test1', 'test2']);
        yield $property1 => $annotation1;

        $property2 = Mockery::mock(ReflectionProperty::class);
        $property2->shouldReceive("getName")
                  ->once()
                  ->withNoArgs()
                  ->andReturn("finalPrince");

        $annotation2 = new Integrationable(['test3', 'test4']);
        yield $property2 => $annotation2;
    }
}
