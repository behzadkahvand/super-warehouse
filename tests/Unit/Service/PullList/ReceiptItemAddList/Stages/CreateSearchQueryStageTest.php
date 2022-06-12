<?php

namespace App\Tests\Unit\Service\PullList\ReceiptItemAddList\Stages;

use App\Entity\ReceiptItem;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\PullList\ReceiptItemAddList\SearchPayload;
use App\Service\PullList\ReceiptItemAddList\Stages\CreateSearchQueryStage;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class CreateSearchQueryStageTest extends BaseUnitTestCase
{
    protected QueryBuilderFilterService|LegacyMockInterface|MockInterface|null $filterServiceMock;

    protected QueryBuilder|LegacyMockInterface|MockInterface|null $queryBuilderMock;

    protected ?CreateSearchQueryStage $createSearchQueryStage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filterServiceMock = Mockery::mock(QueryBuilderFilterService::class);
        $this->queryBuilderMock  = Mockery::mock(QueryBuilder::class);

        $this->createSearchQueryStage = new CreateSearchQueryStage($this->filterServiceMock);
    }

    public function testGetTagAndPriority(): void
    {
        self::assertEquals(
            'app.pipeline_stage.pull_list.receipt_item.add_list',
            $this->createSearchQueryStage::getTag()
        );
        self::assertEquals(20, $this->createSearchQueryStage::getPriority());
    }

    public function testItCanCreateSearchQuery(): void
    {
        $payload = new SearchPayload(2, ['filters'], ['sorts']);

        self::assertNull($payload->getQueryBuilder());

        $this->filterServiceMock->expects('filter')
                                ->with(ReceiptItem::class, ['filter' => ['filters'], 'sort' => ['sorts'],])
                                ->andReturns($this->queryBuilderMock);

        $result = $this->createSearchQueryStage->__invoke($payload);

        self::assertInstanceOf(SearchPayload::class, $result);
        self::assertNotNull($result->getQueryBuilder());
    }
}
