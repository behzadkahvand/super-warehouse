<?php

namespace App\Tests\Unit\Service\ItemSerial;

use App\DTO\AutoStoreItemSerialData;
use App\DTO\CustomStoreItemSerialData;
use App\Entity\Inventory;
use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\ReceiptItemSerial;
use App\Entity\Warehouse;
use App\Events\ItemSerial\ItemBatchSerialsCreatedEvent;
use App\Service\ItemSerial\ItemSerialFactory;
use App\Service\ItemSerial\ItemSerialService;
use App\Service\ItemSerial\ReceiptItemSerialFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ItemSerialServiceTest extends MockeryTestCase
{
    protected ?EntityManagerInterface $entityManagerMock;

    protected ?ItemSerialFactory $itemSerialFactoryMock;

    protected ?ReceiptItemSerialFactory $receiptItemSerialFactory;

    protected ?EventDispatcherInterface $dispatcher;

    protected ItemSerialService $itemSerialService;

    public function setUp(): void
    {
        parent::setUp();

        $this->entityManagerMock        = Mockery::mock(EntityManagerInterface::class);
        $this->itemSerialFactoryMock    = Mockery::mock(ItemSerialFactory::class);
        $this->receiptItemSerialFactory = Mockery::mock(ReceiptItemSerialFactory::class);
        $this->dispatcher               = Mockery::mock(EventDispatcherInterface::class);
        $this->itemSerialService        = new ItemSerialService(
            $this->entityManagerMock,
            $this->itemSerialFactoryMock,
            $this->receiptItemSerialFactory,
            $this->dispatcher
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->itemSerialService);
        $this->entityManagerMock        = null;
        $this->itemSerialFactoryMock    = null;
        $this->receiptItemSerialFactory = null;

        Mockery::close();
    }

    public function testAutoStoreBatchItemSerials(): void
    {
        $itemBatch               = Mockery::mock(ItemBatch::class);
        $autoStoreItemSerialData = Mockery::mock(AutoStoreItemSerialData::class);
        $receiptItemSerial       = Mockery::mock(ReceiptItemSerial::class);
        $receiptItem             = Mockery::mock(ReceiptItem::class);
        $receipt                 = Mockery::mock(Receipt::class);

        $receipt->shouldReceive('getSourceWarehouse')
                ->once()
                ->withNoArgs()
                ->andReturn(new Warehouse());

        $autoStoreItemSerialData->shouldReceive('getItemBatch')
                                ->once()
                                ->withNoArgs()
                                ->andReturn($itemBatch);

        $itemBatch->shouldReceive('getQuantity')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(2);
        $itemBatch->shouldReceive('getItemSerials')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(new ArrayCollection([new ItemSerial()]));
        $itemBatch->shouldReceive('getReceiptItemBatches')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(new ArrayCollection([$receiptItemSerial]));
        $itemBatch->shouldReceive('getInventory')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(new Inventory());
        $itemBatch->shouldReceive('getReceipt')
                  ->once()
                  ->withNoArgs()
                  ->andReturn($receipt);

        $receiptItemSerial->shouldReceive('getReceiptItem')
                          ->once()
                          ->withNoArgs()
                          ->andReturn($receiptItem);

        $this->itemSerialFactoryMock->shouldReceive('create')
                                    ->once()
                                    ->withNoArgs()
                                    ->andReturn(new ItemSerial());

        $this->receiptItemSerialFactory->shouldReceive('create')
                                       ->once()
                                       ->withNoArgs()
                                       ->andReturn(new ReceiptItemSerial());

        $this->entityManagerMock->shouldReceive('persist')
                                ->once()
                                ->with(Mockery::type(ItemSerial::class))
                                ->andReturn();
        $this->entityManagerMock->shouldReceive('persist')
                                ->once()
                                ->with(Mockery::type(ReceiptItemSerial::class))
                                ->andReturn();

        $this->entityManagerMock->shouldReceive('flush')
                                ->once()
                                ->withNoArgs()
                                ->andReturn();

        $this->dispatcher->shouldReceive('dispatch')
                         ->once()
                         ->with(Mockery::type(ItemBatchSerialsCreatedEvent::class))
                         ->andReturn(new stdClass());

        $this->itemSerialService->autoStore($autoStoreItemSerialData);
    }

    public function testCustomStoreBatchItemSerials(): void
    {
        $itemBatch               = Mockery::mock(ItemBatch::class);
        $customStoreItemSerialData = Mockery::mock(CustomStoreItemSerialData::class);
        $receiptItemSerial       = Mockery::mock(ReceiptItemSerial::class);
        $receiptItem             = Mockery::mock(ReceiptItem::class);
        $receipt                 = Mockery::mock(Receipt::class);

        $receipt->shouldReceive('getSourceWarehouse')
                ->once()
                ->withNoArgs()
                ->andReturn(new Warehouse());

        $customStoreItemSerialData->shouldReceive('getItemBatch')
                                ->once()
                                ->withNoArgs()
                                ->andReturn($itemBatch);
        $customStoreItemSerialData->shouldReceive('getSerials')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturn(['test1']);

        $itemBatch->shouldReceive('getReceiptItemBatches')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(new ArrayCollection([$receiptItemSerial]));
        $itemBatch->shouldReceive('getInventory')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(new Inventory());
        $itemBatch->shouldReceive('getReceipt')
                  ->once()
                  ->withNoArgs()
                  ->andReturn($receipt);

        $receiptItemSerial->shouldReceive('getReceiptItem')
                          ->once()
                          ->withNoArgs()
                          ->andReturn($receiptItem);

        $this->itemSerialFactoryMock->shouldReceive('create')
                                    ->once()
                                    ->withNoArgs()
                                    ->andReturn(new ItemSerial());

        $this->receiptItemSerialFactory->shouldReceive('create')
                                       ->once()
                                       ->withNoArgs()
                                       ->andReturn(new ReceiptItemSerial());

        $this->entityManagerMock->shouldReceive('persist')
                                ->once()
                                ->with(Mockery::type(ItemSerial::class))
                                ->andReturn();
        $this->entityManagerMock->shouldReceive('persist')
                                ->once()
                                ->with(Mockery::type(ReceiptItemSerial::class))
                                ->andReturn();

        $this->entityManagerMock->shouldReceive('flush')
                                ->once()
                                ->withNoArgs()
                                ->andReturn();

        $this->dispatcher->shouldReceive('dispatch')
                         ->once()
                         ->with(Mockery::type(ItemBatchSerialsCreatedEvent::class))
                         ->andReturn(new stdClass());

        $this->itemSerialService->customStore($customStoreItemSerialData);
    }
}
