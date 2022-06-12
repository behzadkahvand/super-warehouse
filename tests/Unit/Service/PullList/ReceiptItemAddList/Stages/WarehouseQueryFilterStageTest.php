<?php

namespace App\Tests\Unit\Service\PullList\ReceiptItemAddList\Stages;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\PullList\ReceiptItemAddList\SearchPayload;
use App\Service\PullList\ReceiptItemAddList\Stages\WarehouseQueryFilterStage;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class WarehouseQueryFilterStageTest extends BaseUnitTestCase
{
    protected QueryBuilderFilterService|LegacyMockInterface|MockInterface|null $filterServiceMock;

    protected QueryBuilder|LegacyMockInterface|MockInterface|null $queryBuilderMock;

    protected ?WarehouseQueryFilterStage $warehouseQueryFilterStage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filterServiceMock = Mockery::mock(QueryBuilderFilterService::class);
        $this->queryBuilderMock  = Mockery::mock(QueryBuilder::class);

        $this->warehouseQueryFilterStage = new WarehouseQueryFilterStage($this->filterServiceMock);
    }

    public function testGetTagAndPriority(): void
    {
        self::assertEquals(
            'app.pipeline_stage.pull_list.receipt_item.add_list',
            $this->warehouseQueryFilterStage::getTag()
        );
        self::assertEquals(15, $this->warehouseQueryFilterStage::getPriority());
    }

    public function testItCanAddWarehouseQueryFilterWhenReceiptAliasIsNull(): void
    {
        $payload = new SearchPayload(2, [], []);

        $payload->setQueryBuilder($this->queryBuilderMock);

        $rootAlias    = 'root_alias';
        $receiptAlias = 'Receipt';

        $this->queryBuilderMock->expects('getRootAliases')
                               ->withNoArgs()
                               ->andReturns([$rootAlias]);
        $this->queryBuilderMock->expects('innerJoin')
                               ->with("{$rootAlias}.receipt", $receiptAlias)
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('andWhere')
                               ->with(
                                   sprintf(
                                       '(%1$s.type = :GRType AND IDENTITY(%1$s.sourceWarehouse) = %2$d) OR 
                    (%1$s.type = :STType AND IDENTITY(%1$s.destinationWarehouse) = %2$d)',
                                       $receiptAlias,
                                       2,
                                   )
                               )
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('setParameter')
                               ->with('GRType', ReceiptTypeDictionary::GOOD_RECEIPT)
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('setParameter')
                               ->with('STType', ReceiptTypeDictionary::STOCK_TRANSFER)
                               ->andReturnSelf();

        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Receipt::class)
                                ->andReturnNull();

        $result = $this->warehouseQueryFilterStage->__invoke($payload);

        self::assertInstanceOf(SearchPayload::class, $result);
        self::assertEquals($payload, $result);
    }

    public function testItCanAddWarehouseQueryFilterWhenReceiptAliasIsNotNull(): void
    {
        $payload = new SearchPayload(2, [], []);

        $payload->setQueryBuilder($this->queryBuilderMock);

        $rootAlias    = 'root_alias';
        $receiptAlias = 'receipt_alias';

        $this->queryBuilderMock->expects('getRootAliases')
                               ->withNoArgs()
                               ->andReturns([$rootAlias]);
        $this->queryBuilderMock->expects('andWhere')
                               ->with(
                                   sprintf(
                                       '(%1$s.type = :GRType AND IDENTITY(%1$s.sourceWarehouse) = %2$d) OR 
                    (%1$s.type = :STType AND IDENTITY(%1$s.destinationWarehouse) = %2$d)',
                                       $receiptAlias,
                                       2,
                                   )
                               )
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('setParameter')
                               ->with('GRType', ReceiptTypeDictionary::GOOD_RECEIPT)
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('setParameter')
                               ->with('STType', ReceiptTypeDictionary::STOCK_TRANSFER)
                               ->andReturnSelf();

        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Receipt::class)
                                ->andReturns($receiptAlias);

        $result = $this->warehouseQueryFilterStage->__invoke($payload);

        self::assertInstanceOf(SearchPayload::class, $result);
        self::assertEquals($payload, $result);
    }
}
