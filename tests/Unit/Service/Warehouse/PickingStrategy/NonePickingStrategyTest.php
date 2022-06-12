<?php

namespace App\Tests\Unit\Service\Warehouse\PickingStrategy;

use App\Dictionary\WarehousePickingStrategyDictionary;
use App\Entity\Warehouse;
use App\Service\Warehouse\PickingStrategy\NonePickingStrategy;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Tightenco\Collect\Support\Collection;

final class NonePickingStrategyTest extends MockeryTestCase
{
    public function testSupports(): void
    {
        $warehouse = \Mockery::mock(Warehouse::class);
        $warehouse->shouldReceive('getPickingStrategy')
            ->once()
            ->withNoArgs()
            ->andReturn(WarehousePickingStrategyDictionary::NONE);

        $strategy = new NonePickingStrategy();
        self::assertTrue($strategy->supports($warehouse));
    }

    public function testSupportsFail(): void
    {
        $warehouse = \Mockery::mock(Warehouse::class);
        $warehouse->shouldReceive('getPickingStrategy')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(WarehousePickingStrategyDictionary::FEFO);

        $strategy = new NonePickingStrategy();
        self::assertFalse($strategy->supports($warehouse));
    }

    public function testApplySorting(): void
    {
        $data = \Mockery::mock(Collection::class);
        $data->shouldReceive('sortBy')
                  ->once()
                  ->with(\Mockery::type('Closure'))
                  ->andReturn(collect());

        $strategy = new NonePickingStrategy();
        self::assertInstanceOf(Collection::class, $strategy->applySorting($data));
    }
}
