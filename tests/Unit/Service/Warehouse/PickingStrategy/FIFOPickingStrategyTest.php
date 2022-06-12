<?php

namespace App\Tests\Unit\Service\Warehouse\PickingStrategy;

use App\Dictionary\WarehousePickingStrategyDictionary;
use App\Entity\Warehouse;
use App\Service\Warehouse\PickingStrategy\FIFOPickingStrategy;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Tightenco\Collect\Support\Collection;

final class FIFOPickingStrategyTest extends MockeryTestCase
{
    public function testSupports(): void
    {
        $warehouse = \Mockery::mock(Warehouse::class);
        $warehouse->shouldReceive('getPickingStrategy')
            ->once()
            ->withNoArgs()
            ->andReturn(WarehousePickingStrategyDictionary::FIFO);

        $strategy = new FIFOPickingStrategy();
        self::assertTrue($strategy->supports($warehouse));
    }

    public function testSupportsFail(): void
    {
        $warehouse = \Mockery::mock(Warehouse::class);
        $warehouse->shouldReceive('getPickingStrategy')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(WarehousePickingStrategyDictionary::FEFO);

        $strategy = new FIFOPickingStrategy();
        self::assertFalse($strategy->supports($warehouse));
    }

    public function testApplySorting(): void
    {
        $data = \Mockery::mock(Collection::class);
        $data->shouldReceive('sortBy')
                  ->once()
                  ->with(\Mockery::type('Closure'))
                  ->andReturn(collect());

        $strategy = new FIFOPickingStrategy();
        self::assertInstanceOf(Collection::class, $strategy->applySorting($data));
    }
}
