<?php

namespace App\Tests\Unit\Listeners\ReceiptItem;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Events\ReceiptItem\StoringReceiptItemManuallyEvent;
use App\Exceptions\WarehouseStock\LackOfSellableStockException;
use App\Listeners\ReceiptItem\CheckSellableStockReceiptItemListener;
use App\Service\WarehouseStock\CheckWarehouseStockWithReceiptItem;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class CheckSellableStockReceiptItemListenerTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|CheckWarehouseStockWithReceiptItem|MockInterface|null $checkWarehouseStockMock;

    protected Receipt|LegacyMockInterface|MockInterface|null $receiptMock;

    protected LegacyMockInterface|ReceiptItem|MockInterface|null $receiptItemMock;

    protected ?CheckSellableStockReceiptItemListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkWarehouseStockMock = Mockery::mock(CheckWarehouseStockWithReceiptItem::class);
        $this->receiptMock             = Mockery::mock(Receipt::class);
        $this->receiptItemMock         = Mockery::mock(ReceiptItem::class);

        $this->listener = new CheckSellableStockReceiptItemListener(
            $this->checkWarehouseStockMock
        );
    }

    public function testItCanNotCheckSellableStockWhenReceiptIsNotGoodIssue(): void
    {
        $this->receiptMock->shouldReceive('getType')
                          ->once()
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::STOCK_TRANSFER);

        $this->receiptItemMock->shouldReceive('getReceipt')
                              ->once()
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);

        $event = new StoringReceiptItemManuallyEvent($this->receiptItemMock);

        $this->listener->checkWarehouseSellableStock($event);
    }

    public function testItCanCheckSellableStockWhenQuantityIsGreaterThanStock(): void
    {
        $this->receiptMock->shouldReceive('getType')
                          ->once()
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::GOOD_ISSUE);

        $this->receiptItemMock->shouldReceive('getReceipt')
                              ->once()
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);

        $this->checkWarehouseStockMock->expects('sellableStock')
                                      ->with($this->receiptItemMock)
                                      ->andReturnFalse();

        $event = new StoringReceiptItemManuallyEvent($this->receiptItemMock);

        $this->expectException(LackOfSellableStockException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Warehouse sellable stock is less than shipping quantity!');

        $this->listener->checkWarehouseSellableStock($event);
    }

    public function testItCanCheckSellableStockWhenQuantityIsLessThanAndEqualsToStock(): void
    {
        $this->receiptMock->shouldReceive('getType')
                          ->once()
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::GOOD_ISSUE);

        $this->receiptItemMock->shouldReceive('getReceipt')
                              ->once()
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);

        $this->checkWarehouseStockMock->expects('sellableStock')
                                      ->with($this->receiptItemMock)
                                      ->andReturnTrue();

        $event = new StoringReceiptItemManuallyEvent($this->receiptItemMock);

        $this->listener->checkWarehouseSellableStock($event);
    }
}
