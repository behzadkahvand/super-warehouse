<?php

namespace App\Tests\Unit\Service\PullList\ReceiptItemAddList\Stages;

use App\Service\PullList\ReceiptItemAddList\SearchPayload;
use App\Service\PullList\ReceiptItemAddList\Stages\PullListItemExistenceQueryFilterStage;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class PullListItemExistenceQueryFilterStageTest extends BaseUnitTestCase
{
    protected QueryBuilder|LegacyMockInterface|MockInterface|null $queryBuilderMock;

    protected ?PullListItemExistenceQueryFilterStage $pullListItemExistenceQueryFilterStage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queryBuilderMock = Mockery::mock(QueryBuilder::class);

        $this->pullListItemExistenceQueryFilterStage = new PullListItemExistenceQueryFilterStage();
    }

    public function testGetTagAndPriority(): void
    {
        self::assertEquals(
            'app.pipeline_stage.pull_list.receipt_item.add_list',
            $this->pullListItemExistenceQueryFilterStage::getTag()
        );
        self::assertEquals(10, $this->pullListItemExistenceQueryFilterStage::getPriority());
    }

    public function testItCanAddPullListItemExistenceQueryFilter(): void
    {
        $payload = new SearchPayload(2, [], []);

        $payload->setQueryBuilder($this->queryBuilderMock);

        $rootAlias = 'root_alias';

        $this->queryBuilderMock->expects('getRootAliases')
                               ->withNoArgs()
                               ->andReturns([$rootAlias]);
        $this->queryBuilderMock->expects('leftJoin')
                               ->with("{$rootAlias}.pullListItem", 'PullListItem')
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('andWhere')
                               ->with('PullListItem.id IS NULL')
                               ->andReturnSelf();

        $result = $this->pullListItemExistenceQueryFilterStage->__invoke($payload);

        self::assertInstanceOf(SearchPayload::class, $result);
        self::assertEquals($payload, $result);
    }
}
