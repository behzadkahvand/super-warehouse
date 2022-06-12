<?php

namespace App\Tests\Unit\Service\PickList\Filters;

use App\Entity\Inventory;
use App\Repository\ItemSerialRepository;
use App\Service\PickList\Filters\CreateQueryStage;
use App\Service\PickList\PickListFilterPayload;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class CreateQueryStageTest extends MockeryTestCase
{
    public function testInvoke(): void
    {
        $payload = Mockery::mock(PickListFilterPayload::class);
        $repository = Mockery::mock(ItemSerialRepository::class);
        $queryBuilder = Mockery::mock(QueryBuilder::class);

        $payload->shouldReceive('getInventory')
            ->once()
            ->withNoArgs()
            ->andReturn(new Inventory());

        $payload->shouldReceive('setQueryBuilder')
                ->once()
                ->with($queryBuilder)
                ->andReturnSelf();

        $repository->shouldReceive('getItemSerialsWithInventoryQueryBuilder')
            ->once()
            ->with(Mockery::type(Inventory::class))
            ->andReturn($queryBuilder);

        $stage = new CreateQueryStage($repository);

        $stage($payload);
    }

    public function testGetTag(): void
    {
        self::assertEquals('app.pipeline_stage.pick_list.create.item', CreateQueryStage::getTag());
    }

    public function testGetPriority(): void
    {
        self::assertEquals(40, CreateQueryStage::getPriority());
    }
}
