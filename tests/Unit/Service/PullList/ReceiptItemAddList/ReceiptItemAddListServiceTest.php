<?php

namespace App\Tests\Unit\Service\PullList\ReceiptItemAddList;

use App\Service\PullList\ReceiptItemAddList\ReceiptItemAddListService;
use App\Service\PullList\ReceiptItemAddList\SearchPayload;
use App\Service\PullList\ReceiptItemAddList\Stages\DefaultFilterAndSortStage;
use App\Service\PullList\ReceiptItemAddList\Stages\FilterAndSortMappingStage;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class ReceiptItemAddListServiceTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|MockInterface|FilterAndSortMappingStage|null $filterAndSortMappingStageMock;

    protected LegacyMockInterface|MockInterface|DefaultFilterAndSortStage|null $defaultFilterAndSortStageMock;

    protected QueryBuilder|LegacyMockInterface|MockInterface|null $queryBuilderMock;

    protected AbstractQuery|LegacyMockInterface|MockInterface|null $queryMock;

    protected ?ReceiptItemAddListService $receiptItemAddListService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filterAndSortMappingStageMock = Mockery::mock(FilterAndSortMappingStage::class);
        $this->defaultFilterAndSortStageMock = Mockery::mock(DefaultFilterAndSortStage::class);
        $this->queryBuilderMock              = Mockery::mock(QueryBuilder::class);
        $this->queryMock                     = Mockery::mock(AbstractQuery::class);

        $this->receiptItemAddListService = new ReceiptItemAddListService([
            $this->filterAndSortMappingStageMock,
            $this->defaultFilterAndSortStageMock
        ]);
    }

    public function testItCanGetSearchQuery(): void
    {
        $payload = new SearchPayload(2, [], []);
        $payload->setQueryBuilder($this->queryBuilderMock);

        $this->filterAndSortMappingStageMock->expects('__invoke')
                                            ->with($payload)
                                            ->andReturns($payload);

        $this->defaultFilterAndSortStageMock->expects('__invoke')
                                            ->with($payload)
                                            ->andReturns($payload);

        $this->queryBuilderMock->expects('getQuery')
                               ->withNoArgs()
                               ->andReturns($this->queryMock);

        $this->queryMock->expects('setHint')
                        ->with(Query::HINT_FORCE_PARTIAL_LOAD, true)
                        ->andReturnSelf();

        $result = $this->receiptItemAddListService->get($payload);

        self::assertEquals($this->queryMock, $result);
    }
}
