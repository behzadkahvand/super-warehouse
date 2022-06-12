<?php

namespace App\Tests\Unit\Service\Warehouse;

use App\Entity\Warehouse;
use App\Service\Warehouse\Exceptions\PickingStrategyNotFoundException;
use App\Service\Warehouse\PickingStrategy\FEFOPickingStrategy;
use App\Service\Warehouse\PickingStrategy\FIFOPickingStrategy;
use App\Service\Warehouse\PickingStrategy\LIFOPickingStrategy;
use App\Service\Warehouse\PickingStrategy\NonePickingStrategy;
use App\Service\Warehouse\WarehousePickingStrategyService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Tightenco\Collect\Support\Collection;

final class WarehousePickingStrategyServiceTest extends MockeryTestCase
{
    public function testApply(): void
    {
        $FIFO = Mockery::mock(FIFOPickingStrategy::class);
        $FEFO = Mockery::mock(FEFOPickingStrategy::class);
        $LIFO = Mockery::mock(LIFOPickingStrategy::class);
        $NONE = Mockery::mock(NonePickingStrategy::class);
        $warehouse = Mockery::mock(Warehouse::class);
        $data = Mockery::mock(Collection::class);

        $strategies = [$FIFO, $FEFO, $LIFO, $NONE];

        $FIFO->shouldReceive('supports')
            ->once()
            ->with($warehouse)
            ->andReturnFalse();

        $FEFO->shouldReceive('supports')
             ->once()
             ->with($warehouse)
             ->andReturnFalse();

        $LIFO->shouldReceive('supports')
             ->once()
             ->with($warehouse)
             ->andReturnFalse();

        $NONE->shouldReceive('supports')
             ->once()
             ->with($warehouse)
             ->andReturnTrue();

        $NONE->shouldReceive('applySorting')
             ->once()
             ->with($data)
             ->andReturn(collect());

        $service = new WarehousePickingStrategyService($strategies);
        $service->apply($warehouse, $data);
    }

    public function testApplyThrowException(): void
    {
        $FIFO = Mockery::mock(FIFOPickingStrategy::class);
        $FEFO = Mockery::mock(FEFOPickingStrategy::class);
        $LIFO = Mockery::mock(LIFOPickingStrategy::class);
        $NONE = Mockery::mock(NonePickingStrategy::class);
        $warehouse = Mockery::mock(Warehouse::class);
        $data = Mockery::mock(Collection::class);

        $strategies = [$FIFO, $FEFO, $LIFO, $NONE];

        $FIFO->shouldReceive('supports')
             ->once()
             ->with($warehouse)
             ->andReturnFalse();

        $FEFO->shouldReceive('supports')
             ->once()
             ->with($warehouse)
             ->andReturnFalse();

        $LIFO->shouldReceive('supports')
             ->once()
             ->with($warehouse)
             ->andReturnFalse();

        $NONE->shouldReceive('supports')
             ->once()
             ->with($warehouse)
             ->andReturnFalse();

        self::expectException(PickingStrategyNotFoundException::class);

        $service = new WarehousePickingStrategyService($strategies);
        $service->apply($warehouse, $data);
    }
}
