<?php

namespace App\Tests\Unit\Service\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Inventory;
use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\Receipt\STInboundReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use App\Entity\ReceiptItem;
use App\Entity\ReceiptItemBatch;
use App\Entity\ReceiptItemSerial;
use App\Entity\Warehouse;
use App\Service\ItemBatch\ReceiptItemBatchFactory;
use App\Service\ItemSerial\ReceiptItemSerialFactory;
use App\Service\Receipt\ReceiptItemFactory;
use App\Service\Receipt\STInboundReceiptFactory;
use App\Service\Receipt\STInboundReceiptService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mockery;

class STInboundReceiptServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|EntityManagerInterface|Mockery\MockInterface|null $manager;

    protected STInboundReceiptFactory|Mockery\LegacyMockInterface|Mockery\MockInterface|null $stInboundReceiptFactory;

    protected Mockery\LegacyMockInterface|ReceiptItemFactory|Mockery\MockInterface|null $receiptItemFactory;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|ReceiptItemSerialFactory|null $receiptItemSerialFactory;

    protected ReceiptItemBatchFactory|Mockery\LegacyMockInterface|Mockery\MockInterface|null $receiptItemBatchFactory;

    protected STInboundReceiptService|null $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager                  = Mockery::mock(EntityManagerInterface::class);
        $this->stInboundReceiptFactory  = Mockery::mock(STInboundReceiptFactory::class);
        $this->receiptItemFactory       = Mockery::mock(ReceiptItemFactory::class);
        $this->receiptItemSerialFactory = Mockery::mock(ReceiptItemSerialFactory::class);
        $this->receiptItemBatchFactory  = Mockery::mock(ReceiptItemBatchFactory::class);

        $this->sut = new STInboundReceiptService(
            $this->manager,
            $this->stInboundReceiptFactory,
            $this->receiptItemFactory,
            $this->receiptItemSerialFactory,
            $this->receiptItemBatchFactory
        );
    }

    public function testItCanCreateReceipt(): void
    {
        $stOutBoundReceipt = Mockery::mock(STOutboundReceipt::class);
        $stInboundReceipt  = Mockery::mock(STInboundReceipt::class);

        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturn();

        $this->stInboundReceiptFactory->expects("create")
                                      ->withNoArgs()
                                      ->andReturn($stInboundReceipt);

        $sourceWarehouse = Mockery::mock(Warehouse::class);
        $stOutBoundReceipt->expects("getSourceWarehouse")
                          ->withNoArgs()
                          ->andReturn($sourceWarehouse);
        $stOutBoundReceipt->expects("getDestinationWarehouse")
                          ->withNoArgs()
                          ->andReturn(null);

        $stInboundReceipt->expects("setStatus")
                         ->with(ReceiptStatusDictionary::APPROVED)
                         ->andReturnSelf();
        $stInboundReceipt->expects("setReference")
                         ->with($stOutBoundReceipt)
                         ->andReturnSelf();
        $stInboundReceipt->expects("setSourceWarehouse")
                         ->with($sourceWarehouse)
                         ->andReturnSelf();
        $stInboundReceipt->expects("setDestinationWarehouse")
                         ->with(null)
                         ->andReturnSelf();

        $this->manager->expects("persist")
                      ->with($stInboundReceipt)
                      ->andReturn();

        $stOutBoundReceipt->expects("setInboundReceipt")
                          ->with($stInboundReceipt)
                          ->andReturnSelf();

        $receiptItemOutbound = Mockery::mock(ReceiptItem::class);
        $receiptItemInbound  = Mockery::mock(ReceiptItem::class);

        $stOutBoundReceipt->expects("getReceiptItems")
                          ->withNoArgs()
                          ->andReturn(new ArrayCollection([$receiptItemOutbound]));

        $this->receiptItemFactory->expects("create")
                                 ->withNoArgs()
                                 ->andReturn($receiptItemInbound);

        $inventory = Mockery::mock(Inventory::class);
        $receiptItemOutbound->expects("getInventory")
                            ->withNoArgs()
                            ->andReturn($inventory);
        $receiptItemOutbound->expects("getQuantity")
                            ->withNoArgs()
                            ->andReturn(1);

        $receiptItemInbound->expects("setStatus")
                           ->with(ReceiptStatusDictionary::APPROVED)
                           ->andReturnSelf();
        $receiptItemInbound->expects("setInventory")
                           ->with($inventory)
                           ->andReturnSelf();
        $receiptItemInbound->expects("setQuantity")
                           ->with(1)
                           ->andReturnSelf();

        $this->manager->expects("persist")
                      ->with($receiptItemInbound)
                      ->andReturn();

        $stInboundReceipt->expects("addReceiptItem")
                         ->with($receiptItemInbound)
                         ->andReturnSelf();

        $receiptItemBatchOutbound = Mockery::mock(ReceiptItemBatch::class);
        $receiptItemBatchInbound  = Mockery::mock(ReceiptItemBatch::class);

        $receiptItemOutbound->expects("getReceiptItemBatches")
                            ->withNoArgs()
                            ->andReturn(new ArrayCollection([$receiptItemBatchOutbound]));

        $this->receiptItemBatchFactory->expects("create")
                                      ->withNoArgs()
                                      ->andReturn($receiptItemBatchInbound);

        $itemBatch = Mockery::mock(ItemBatch::class);
        $receiptItemBatchOutbound->expects("getItemBatch")
                                 ->withNoArgs()
                                 ->andReturn($itemBatch);

        $receiptItemBatchInbound->expects("setItemBatch")
                                ->with($itemBatch)
                                ->andReturnSelf();

        $this->manager->expects("persist")
                      ->with($receiptItemBatchInbound)
                      ->andReturn();

        $receiptItemInbound->expects("addReceiptItemBatch")
                           ->with($receiptItemBatchInbound)
                           ->andReturnSelf();

        $receiptItemSerialOutbound = Mockery::mock(ReceiptItemSerial::class);
        $receiptItemSerialInbound  = Mockery::mock(ReceiptItemSerial::class);

        $receiptItemOutbound->expects("getReceiptItemSerials")
                            ->withNoArgs()
                            ->andReturn(new ArrayCollection([$receiptItemSerialOutbound]));

        $this->receiptItemSerialFactory->expects("create")
                                       ->withNoArgs()
                                       ->andReturn($receiptItemSerialInbound);

        $itemSerial = Mockery::mock(ItemSerial::class);
        $receiptItemSerialOutbound->expects("getItemSerial")
                                  ->withNoArgs()
                                  ->andReturn($itemSerial);

        $receiptItemSerialInbound->expects("setItemSerial")
                                 ->with($itemSerial)
                                 ->andReturnSelf();

        $this->manager->expects("persist")
                      ->with($receiptItemSerialInbound)
                      ->andReturn();

        $receiptItemInbound->expects("addReceiptItemSerial")
                           ->with($receiptItemSerialInbound)
                           ->andReturnSelf();

        $this->manager->expects("flush")
                      ->withNoArgs()
                      ->andReturn();
        $this->manager->expects("commit")
                      ->withNoArgs()
                      ->andReturn();

        $this->sut->create($stOutBoundReceipt);
    }

    public function testRollbackWhenFail(): void
    {
        $stOutBoundReceipt = Mockery::mock(STOutboundReceipt::class);
        $stInBoundReceipt = Mockery::mock(STInboundReceipt::class);

        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturn();

        $this->stInboundReceiptFactory->expects("create")
                                      ->withNoArgs()
                                      ->andReturn($stInBoundReceipt);

        $stOutBoundReceipt->expects("getSourceWarehouse")
                          ->withNoArgs()
                          ->andThrow(new Exception("test"));

        $stInBoundReceipt->expects("setStatus")
                         ->with(ReceiptStatusDictionary::APPROVED)
                         ->andReturnSelf();
        $stInBoundReceipt->expects("setReference")
                         ->with($stOutBoundReceipt)
                         ->andReturnSelf();

        $this->manager->expects("close")
                      ->withNoArgs()
                      ->andReturn();
        $this->manager->expects("rollback")
                      ->withNoArgs()
                      ->andReturn();

        self::expectException(Exception::class);

        $this->sut->create($stOutBoundReceipt);
    }
}
