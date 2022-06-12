<?php

namespace App\Tests\Unit\Service\PickList\Filters;

use App\Entity\Inventory;
use App\Entity\ItemSerial;
use App\Repository\PickListRepository;
use App\Service\PickList\Filters\FilterReserveStockStage;
use App\Service\PickList\PickListFilterPayload;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Tightenco\Collect\Support\Collection;

final class FilterReserveStockStageTest extends MockeryTestCase
{
    public function testInvoke(): void
    {
        $payload    = Mockery::mock(PickListFilterPayload::class);
        $repository = Mockery::mock(PickListRepository::class);

        $result = [[
            new ItemSerial(),
            'total' => 1,
        ]];

        $payload->shouldReceive('getResult')
                ->once()
                ->withNoArgs()
                ->andReturn(collect($result));

        $payload->shouldReceive('getInventory')
                ->once()
                ->withNoArgs()
                ->andReturn(new Inventory());

        $payload->shouldReceive('setResult')
                ->once()
                ->with(Mockery::type(Collection::class))
                ->andReturnSelf();

        $repository->shouldReceive('findPickListsForInventoryWithNotCloseStatus')
                   ->once()
                   ->with(Mockery::type(Inventory::class))
                   ->andReturn([]);

        $stage = new FilterReserveStockStage($repository);

        $stage($payload);
    }

    public function testGetTag(): void
    {
        self::assertEquals('app.pipeline_stage.pick_list.create.item', FilterReserveStockStage::getTag());
    }

    public function testGetPriority(): void
    {
        self::assertEquals(20, FilterReserveStockStage::getPriority());
    }
}
