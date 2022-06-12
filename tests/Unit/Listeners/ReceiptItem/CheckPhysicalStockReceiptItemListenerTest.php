<?php

namespace App\Tests\Unit\Listeners\ReceiptItem;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Events\ReceiptItem\StoringReceiptItemManuallyEvent;
use App\Exceptions\WarehouseStock\LackOfPhysicalStockException;
use App\Listeners\ReceiptItem\CheckPhysicalStockReceiptItemListener;
use App\Service\WarehouseStock\CheckWarehouseStockWithReceiptItem;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class CheckPhysicalStockReceiptItemListenerTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|CheckWarehouseStockWithReceiptItem|MockInterface|null $checkWarehouseStockMock;

    protected Receipt|LegacyMockInterface|MockInterface|null $receiptMock;

    protected LegacyMockInterface|ReceiptItem|MockInterface|null $receiptItemMock;

    protected ?CheckPhysicalStockReceiptItemListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkWarehouseStockMock = Mockery::mock(CheckWarehouseStockWithReceiptItem::class);
        $this->receiptMock             = Mockery::mock(Receipt::class);
        $this->receiptItemMock         = Mockery::mock(ReceiptItem::class);

        $this->listener = new CheckPhysicalStockReceiptItemListener(
            $this->checkWarehouseStockMock
        );
    }

    public function testItCanNotCheckPhysicalStockWhenReceiptIsNotStockTransfer(): void
    {
        $this->receiptMock->expects('getType')
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::GOOD_ISSUE);

        $this->receiptItemMock->expects('getReceipt')
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);

        $event = new StoringReceiptItemManuallyEvent($this->receiptItemMock);

        $this->listener->checkWarehousePhysicalStock($event);
    }

    public function testItCanCheckPhysicalStockWhenQuantityIsGreaterThanStock(): void
    {
        $this->receiptMock->expects('getType')
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::STOCK_TRANSFER);

        $this->receiptItemMock->expects('getReceipt')
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);

        $this->checkWarehouseStockMock->expects('physicalStock')
                                      ->with($this->receiptItemMock)
                                      ->andReturnFalse();

        $event = new StoringReceiptItemManuallyEvent($this->receiptItemMock);

        $this->expectException(LackOfPhysicalStockException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Warehouse physical stock is less than shipping quantity!');

        $this->listener->checkWarehousePhysicalStock($event);
    }

    public function testItCanCheckPhysicalStockWhenQuantityIsLessThanOrEqualsToStock(): void
    {
        $this->receiptMock->expects('getType')
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::STOCK_TRANSFER);

        $this->receiptItemMock->expects('getReceipt')
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);

        $this->checkWarehouseStockMock->expects('physicalStock')
                                      ->with($this->receiptItemMock)
                                      ->andReturnTrue();

        $event = new StoringReceiptItemManuallyEvent($this->receiptItemMock);

        $this->listener->checkWarehousePhysicalStock($event);
    }
}
