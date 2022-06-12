<?php

namespace App\Tests\Unit\Service\PullList\ReceiptItemAddList\Stages;

use App\Entity\Inventory;
use App\Entity\Product;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\PullList\ReceiptItemAddList\SearchPayload;
use App\Service\PullList\ReceiptItemAddList\Stages\EagerLoadingQueryStage;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class EagerLoadingQueryStageTest extends BaseUnitTestCase
{
    protected QueryBuilderFilterService|LegacyMockInterface|MockInterface|null $filterServiceMock;

    protected QueryBuilder|LegacyMockInterface|MockInterface|null $queryBuilderMock;

    protected ?EagerLoadingQueryStage $eagerLoadingQueryStage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filterServiceMock = Mockery::mock(QueryBuilderFilterService::class);
        $this->queryBuilderMock  = Mockery::mock(QueryBuilder::class);

        $this->eagerLoadingQueryStage = new EagerLoadingQueryStage($this->filterServiceMock);
    }

    public function testGetTagAndPriority(): void
    {
        self::assertEquals(
            'app.pipeline_stage.pull_list.receipt_item.add_list',
            $this->eagerLoadingQueryStage::getTag()
        );
        self::assertEquals(-1, $this->eagerLoadingQueryStage::getPriority());
    }

    public function testItCanPerformEagerLoadingOnQueryWhenProductAndInventoryJoinsExist(): void
    {
        $payload = new SearchPayload(2, [], []);

        $payload->setQueryBuilder($this->queryBuilderMock);

        $rootAlias      = 'root_alias';
        $receiptAlias   = 'receipt_alias';
        $inventoryAlias = 'inventory_alias';
        $productAlias   = 'product_alias';

        $this->queryBuilderMock->expects('getRootAliases')
                               ->withNoArgs()
                               ->andReturns([$rootAlias]);
        $this->queryBuilderMock->expects('select')
                               ->with("Partial {$rootAlias}.{id, quantity}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$receiptAlias}.{id}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$inventoryAlias}.{id}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$productAlias}.{id}")
                               ->andReturnSelf();

        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Receipt::class)
                                ->andReturns($receiptAlias);
        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Inventory::class)
                                ->andReturns($inventoryAlias);
        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(Inventory::class, Product::class)
                                ->andReturns($productAlias);

        $result = $this->eagerLoadingQueryStage->__invoke($payload);

        self::assertInstanceOf(SearchPayload::class, $result);
        self::assertEquals($payload, $result);
    }

    public function testItCanPerformEagerLoadingOnQueryWhenProductAndInventoryJoinsNotExist(): void
    {
        $payload = new SearchPayload(2, [], []);

        $payload->setQueryBuilder($this->queryBuilderMock);

        $rootAlias      = 'root_alias';
        $receiptAlias   = 'receipt_alias';
        $inventoryAlias = 'Inventory';
        $productAlias   = 'Product';

        $this->queryBuilderMock->expects('getRootAliases')
                               ->withNoArgs()
                               ->andReturns([$rootAlias]);
        $this->queryBuilderMock->expects('leftJoin')
                               ->with("{$rootAlias}.inventory", 'Inventory')
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('leftJoin')
                               ->with("{$inventoryAlias}.product", 'Product')
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('select')
                               ->with("Partial {$rootAlias}.{id, quantity}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$receiptAlias}.{id}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$inventoryAlias}.{id}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$productAlias}.{id}")
                               ->andReturnSelf();

        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Receipt::class)
                                ->andReturns($receiptAlias);
        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Inventory::class)
                                ->andReturnNull();
        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(Inventory::class, Product::class)
                                ->andReturnNull();

        $result = $this->eagerLoadingQueryStage->__invoke($payload);

        self::assertInstanceOf(SearchPayload::class, $result);
        self::assertEquals($payload, $result);
    }

    public function testItCanPerformEagerLoadingOnQueryWhenInventoryJoinExist(): void
    {
        $payload = new SearchPayload(2, [], []);

        $payload->setQueryBuilder($this->queryBuilderMock);

        $rootAlias      = 'root_alias';
        $receiptAlias   = 'receipt_alias';
        $inventoryAlias = 'inventory_alias';
        $productAlias   = 'Product';

        $this->queryBuilderMock->expects('getRootAliases')
                               ->withNoArgs()
                               ->andReturns([$rootAlias]);
        $this->queryBuilderMock->expects('leftJoin')
                               ->with("{$inventoryAlias}.product", 'Product')
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('select')
                               ->with("Partial {$rootAlias}.{id, quantity}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$receiptAlias}.{id}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$inventoryAlias}.{id}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$productAlias}.{id}")
                               ->andReturnSelf();

        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Receipt::class)
                                ->andReturns($receiptAlias);
        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Inventory::class)
                                ->andReturns($inventoryAlias);
        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(Inventory::class, Product::class)
                                ->andReturnNull();

        $result = $this->eagerLoadingQueryStage->__invoke($payload);

        self::assertInstanceOf(SearchPayload::class, $result);
        self::assertEquals($payload, $result);
    }

    public function testItCanPerformEagerLoadingOnQueryWhenProductJoinExist(): void
    {
        $payload = new SearchPayload(2, [], []);

        $payload->setQueryBuilder($this->queryBuilderMock);

        $rootAlias      = 'root_alias';
        $receiptAlias   = 'receipt_alias';
        $inventoryAlias = 'Inventory';
        $productAlias   = 'product_alias';

        $this->queryBuilderMock->expects('getRootAliases')
                               ->withNoArgs()
                               ->andReturns([$rootAlias]);
        $this->queryBuilderMock->expects('leftJoin')
                               ->with("{$rootAlias}.inventory", 'Inventory')
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('select')
                               ->with("Partial {$rootAlias}.{id, quantity}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$receiptAlias}.{id}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$inventoryAlias}.{id}")
                               ->andReturnSelf();
        $this->queryBuilderMock->expects('addSelect')
                               ->with("Partial {$productAlias}.{id}")
                               ->andReturnSelf();

        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Receipt::class)
                                ->andReturns($receiptAlias);
        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(ReceiptItem::class, Inventory::class)
                                ->andReturnNull();
        $this->filterServiceMock->expects('getJoinAlias')
                                ->with(Inventory::class, Product::class)
                                ->andReturns($productAlias);

        $result = $this->eagerLoadingQueryStage->__invoke($payload);

        self::assertInstanceOf(SearchPayload::class, $result);
        self::assertEquals($payload, $result);
    }
}
