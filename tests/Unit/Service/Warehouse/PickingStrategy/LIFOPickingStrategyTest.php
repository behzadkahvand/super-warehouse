<?php

namespace App\Tests\Unit\Service\Warehouse\PickingStrategy;

use App\Dictionary\WarehousePickingStrategyDictionary;
use App\Entity\Warehouse;
use App\Service\Warehouse\PickingStrategy\LIFOPickingStrategy;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Tightenco\Collect\Support\Collection;

final class LIFOPickingStrategyTest extends MockeryTestCase
{
    public function testSupports(): void
    {
        $warehouse = \Mockery::mock(Warehouse::class);
        $warehouse->shouldReceive('getPickingStrategy')
            ->once()
            ->withNoArgs()
            ->andReturn(WarehousePickingStrategyDictionary::LIFO);

        $strategy = new LIFOPickingStrategy();
        self::assertTrue($strategy->supports($warehouse));
    }

    public function testSupportsFail(): void
    {
        $warehouse = \Mockery::mock(Warehouse::class);
        $warehouse->shouldReceive('getPickingStrategy')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(WarehousePickingStrategyDictionary::FIFO);

        $strategy = new LIFOPickingStrategy();
        self::assertFalse($strategy->supports($warehouse));
    }

    public function testApplySorting(): void
    {
        $data = \Mockery::mock(Collection::class);
        $data->shouldReceive('sortByDesc')
                  ->once()
                  ->with(\Mockery::type('Closure'))
                  ->andReturn(collect());

        $strategy = new LIFOPickingStrategy();
        self::assertInstanceOf(Collection::class, $strategy->applySorting($data));
    }
}
