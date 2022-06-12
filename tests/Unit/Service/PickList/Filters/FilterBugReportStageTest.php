<?php

namespace App\Tests\Unit\Service\PickList\Filters;

use App\Entity\Inventory;
use App\Repository\PickListBugReportRepository;
use App\Service\PickList\Filters\FilterBugReportStage;
use App\Service\PickList\PickListFilterPayload;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class FilterBugReportStageTest extends MockeryTestCase
{
    public function testInvoke(): void
    {
        $payload = Mockery::mock(PickListFilterPayload::class);
        $repository = Mockery::mock(PickListBugReportRepository::class);
        $queryBuilder = Mockery::mock(QueryBuilder::class);

        $queryBuilder->shouldReceive('getRootAliases')
            ->once()
            ->withNoArgs()
            ->andReturn(['itemSerial']);

        $queryBuilder->shouldReceive('expr')
                     ->once()
                     ->withNoArgs()
                     ->andReturn(new Expr());

        $queryBuilder->shouldReceive('andWhere')
                     ->once()
                     ->with(Mockery::type(Expr\Func::class))
                     ->andReturnSelf();

        $queryBuilder->shouldReceive('setParameter')
                     ->once()
                     ->with(Mockery::type('string'), [10])
                     ->andReturnSelf();

        $payload->shouldReceive('getInventory')
            ->once()
            ->withNoArgs()
            ->andReturn(new Inventory());

        $payload->shouldReceive('getQueryBuilder')
                ->once()
                ->withNoArgs()
                ->andReturn($queryBuilder);

        $payload->shouldReceive('setQueryBuilder')
                ->once()
                ->with($queryBuilder)
                ->andReturnSelf();

        $repository->shouldReceive('findStorageBinsForInventoryWithNotDoneStatus')
            ->once()
            ->with(Mockery::type(Inventory::class))
            ->andReturn([10]);

        $stage = new FilterBugReportStage($repository);

        $stage($payload);
    }

    public function testGetTag(): void
    {
        self::assertEquals('app.pipeline_stage.pick_list.create.item', FilterBugReportStage::getTag());
    }

    public function testGetPriority(): void
    {
        self::assertEquals(30, FilterBugReportStage::getPriority());
    }
}
