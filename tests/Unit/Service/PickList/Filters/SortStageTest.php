<?php

namespace App\Tests\Unit\Service\PickList\Filters;

use App\Entity\Warehouse;
use App\Service\PickList\Filters\SortStage;
use App\Service\PickList\PickListFilterPayload;
use App\Service\Warehouse\WarehousePickingStrategyService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class SortStageTest extends MockeryTestCase
{
    public function testInvoke(): void
    {
        $payload = Mockery::mock(PickListFilterPayload::class);
        $service = Mockery::mock(WarehousePickingStrategyService::class);
        $data = collect([]);

        $payload->shouldReceive('getWarehouse')
            ->once()
            ->withNoArgs()
            ->andReturn(new Warehouse());

        $payload->shouldReceive('getResult')
                ->once()
                ->withNoArgs()
                ->andReturn($data);

        $payload->shouldReceive('setResult')
                ->once()
                ->with($data)
                ->andReturnSelf();

        $service->shouldReceive('apply')
            ->once()
            ->with(Mockery::type(Warehouse::class), $data)
            ->andReturn($data);

        $stage = new SortStage($service);

        $stage($payload);
    }

    public function testGetTag(): void
    {
        self::assertEquals('app.pipeline_stage.pick_list.create.item', SortStage::getTag());
    }

    public function testGetPriority(): void
    {
        self::assertEquals(10, SortStage::getPriority());
    }
}
