<?php

namespace App\Tests\Unit\Service\Shipment;

use App\Dictionary\ShipmentItemStockTypeDictionary;
use App\Dictionary\ShipmentStatusDictionary;
use App\DTO\GIShipmentReceiptData;
use App\DTO\Integration\ShipmentItemData;
use App\Entity\Inventory;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\Shipment;
use App\Entity\ShipmentItem;
use App\Entity\Warehouse;
use App\Repository\InventoryRepository;
use App\Repository\ShipmentRepository;
use App\Repository\WarehouseRepository;
use App\Service\Receipt\GIShipmentReceiptService;
use App\Service\Shipment\DTO\ShipmentData;
use App\Service\Shipment\ShipmentFactory;
use App\Service\Shipment\ShipmentItemFactory;
use App\Service\Shipment\ShipmentUpsertService;
use App\Tests\Unit\BaseUnitTestCase;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class ShipmentUpsertServiceTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $em;

    protected ShipmentRepository|LegacyMockInterface|MockInterface|null $shipmentRepository;

    protected ShipmentFactory|LegacyMockInterface|MockInterface|null $shipmentFactory;

    protected ShipmentData|LegacyMockInterface|MockInterface|null $dataMock;

    protected ?ShipmentUpsertService $sut;

    protected InventoryRepository|LegacyMockInterface|MockInterface|null $inventoryRepository;

    protected ShipmentItemFactory|LegacyMockInterface|MockInterface|null $shipmentItemFactory;

    protected WarehouseRepository|LegacyMockInterface|MockInterface|null $warehouseRepository;

    protected LegacyMockInterface|GIShipmentReceiptService|MockInterface|null $GIShipmentReceiptService;

    protected LegacyMockInterface|Shipment|MockInterface|null $shipment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em                       = Mockery::mock(EntityManagerInterface::class);
        $this->shipmentRepository       = Mockery::mock(ShipmentRepository::class);
        $this->shipmentFactory          = Mockery::mock(ShipmentFactory::class);
        $this->shipmentItemFactory      = Mockery::mock(ShipmentItemFactory::class);
        $this->inventoryRepository      = Mockery::mock(InventoryRepository::class);
        $this->GIShipmentReceiptService = Mockery::mock(GIShipmentReceiptService::class);
        $this->warehouseRepository      = Mockery::mock(WarehouseRepository::class);
        $this->dataMock                 = Mockery::mock(ShipmentData::class);
        $this->shipment                 = Mockery::mock(Shipment::class);

        $this->sut = new ShipmentUpsertService(
            $this->em,
            $this->shipmentRepository,
            $this->shipmentFactory,
            $this->shipmentItemFactory,
            $this->inventoryRepository,
            $this->GIShipmentReceiptService,
            $this->warehouseRepository,
        );
    }

    public function testItCanCreate(): void
    {
        $this->em->shouldReceive('beginTransaction')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        $this->shipmentFactory->expects('create')->withNoArgs()->andReturns($this->shipment);

        $this->dataMock->expects('getWarehouseId')->withNoArgs()->andReturns(1);
        $this->dataMock->expects('getId')->withNoArgs()->andReturns(1);
        $this->setDataMock();

        $this->em->expects('persist')->with($this->shipment)->andReturns();

        $items = [
            (new ShipmentItemData())->setInventoryId(1)
                                    ->setQuantity(10)
                                    ->setId(1)
                                    ->setStockType(ShipmentItemStockTypeDictionary::SELLER),
        ];
        $this->dataMock->expects('getItems')->withNoArgs()->andReturns($items);

        $inventory = Mockery::mock(Inventory::class);
        $this->inventoryRepository->expects('find')->with(1)->andReturns($inventory);

        $shipmentItem = Mockery::mock(ShipmentItem::class);
        $shipmentItem->expects('setId')->with(1)->andReturnSelf();
        $shipmentItem->expects('setInventory')->with($inventory)->andReturnSelf();
        $shipmentItem->expects('setQuantity')->with(10)->andReturnSelf();
        $shipmentItem->expects('setStockType')->with(ShipmentItemStockTypeDictionary::SELLER)->andReturnSelf();

        $this->shipmentItemFactory->expects('create')->withNoArgs()->andReturns($shipmentItem);

        $this->shipment->expects('addShipmentItem')->with($shipmentItem)->andReturnSelf();

        $this->em->expects('persist')->with($shipmentItem)->andReturns();

        $warehouse = Mockery::mock(Warehouse::class);
        $this->warehouseRepository->expects('find')->with(1)->andReturn($warehouse);

        $receipt = Mockery::mock(Receipt::class);
        $this->GIShipmentReceiptService->expects('create')->with(GIShipmentReceiptData::class)->andReturn($receipt);

        $this->em->expects('flush')->withNoArgs()->andReturns();
        $this->em->shouldReceive('commit')->once()->withNoArgs()->andReturn();

        $this->sut->create($this->dataMock);
    }

    public function testIteCanUpdateShipmentItems(): void
    {
        $item = Mockery::mock(ShipmentItem::class);

        $this->shipment->expects('addShipmentItem')
                                 ->with($item)
                                 ->andReturnSelf();

        $receipt = Mockery::mock(Receipt::class);
        $this->shipment->expects('getReceipt')
                       ->withNoArgs()
                       ->andReturn($receipt);

        $receiptItem = Mockery::mock(ReceiptItem::class);
        $item->expects('getReceiptItem')
                       ->withNoArgs()
                       ->andReturn($receiptItem);

        $receiptItem->expects('setReceipt')
                       ->with($receipt)
                       ->andReturnSelf();

        $this->sut->updateShipmentItems($this->shipment, [$item]);
    }

    public function testCreateFailedWhenInventoryNotFound(): void
    {
        $this->em->shouldReceive('beginTransaction')
                 ->once()
                 ->withNoArgs()
                 ->andReturn();
        $this->em->shouldReceive('close')->once()->withNoArgs()->andReturn();
        $this->em->shouldReceive('rollback')->once()->withNoArgs()->andReturn();

        $this->shipmentFactory->expects('create')->withNoArgs()->andReturns($this->shipment);

        $this->dataMock->expects('getId')->withNoArgs()->andReturns(1);
        $this->setDataMock();

        $this->em->expects('persist')->with($this->shipment)->andReturns();

        $items = [
            (new ShipmentItemData())->setInventoryId(1)
                                    ->setQuantity(10)
                                    ->setId(1),
        ];
        $this->dataMock->expects('getItems')->withNoArgs()->andReturns($items);

        $this->inventoryRepository->expects('find')->with(1)->andReturns(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage("Shipment Item Inventory not found!");

        $this->sut->create($this->dataMock);
    }

    public function testCreateFailedWhenDefaultShippingWarehouseNotFound(): void
    {
        $this->em->shouldReceive('beginTransaction')
                 ->once()
                 ->withNoArgs()
                 ->andReturn();
        $this->em->shouldReceive('close')->once()->withNoArgs()->andReturn();
        $this->em->shouldReceive('rollback')->once()->withNoArgs()->andReturn();

        $this->shipmentFactory->expects('create')->withNoArgs()->andReturns($this->shipment);

        $this->dataMock->expects('getWarehouseId')->withNoArgs()->andReturns(1);
        $this->dataMock->expects('getId')->withNoArgs()->andReturns(1);
        $this->setDataMock();

        $this->em->expects('persist')->with($this->shipment)->andReturns();

        $items = [
            (new ShipmentItemData())->setInventoryId(1)
                                    ->setQuantity(10)
                                    ->setId(1)
                                    ->setStockType(ShipmentItemStockTypeDictionary::SALEABLE),
        ];
        $this->dataMock->expects('getItems')->withNoArgs()->andReturns($items);

        $inventory = Mockery::mock(Inventory::class);
        $this->inventoryRepository->expects('find')->with(1)->andReturns($inventory);

        $shipmentItem = Mockery::mock(ShipmentItem::class);
        $shipmentItem->expects('setId')->with(1)->andReturnSelf();
        $shipmentItem->expects('setInventory')->with($inventory)->andReturnSelf();
        $shipmentItem->expects('setQuantity')->with(10)->andReturnSelf();
        $shipmentItem->expects('setStockType')->with(ShipmentItemStockTypeDictionary::SALEABLE)->andReturnSelf();

        $this->shipmentItemFactory->expects('create')->withNoArgs()->andReturns($shipmentItem);

        $this->shipment->expects('addShipmentItem')->with($shipmentItem)->andReturnSelf();

        $this->em->expects('persist')->with($shipmentItem)->andReturns();

        $this->warehouseRepository->expects('find')->with(1)->andReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage("shipping warehouse not found!");

        $this->sut->create($this->dataMock);
    }

    public function testItCanUpdate(): void
    {
        $this->em->shouldReceive('beginTransaction')
                 ->once()
                 ->withNoArgs()
                 ->andReturn();
        $this->em->shouldReceive('commit')->once()->withNoArgs()->andReturn();

        $this->shipmentRepository->expects('find')->with(1)->andReturns($this->shipment);

        $this->dataMock->shouldReceive('getId')->twice()->withNoArgs()->andReturns(1);
        $this->setDataMock();

        $this->shipment->expects('getStatus')->withNoArgs()->andReturns(ShipmentStatusDictionary::CANCELED);

        $this->GIShipmentReceiptService->expects("cancel")
                                       ->with($this->shipment)
                                       ->andReturn();

        $this->em->expects('flush')->withNoArgs()->andReturns();

        $this->sut->update($this->dataMock);
    }

    public function testUpdateFailedWhenShipmentNotFound(): void
    {
        $this->em->shouldReceive('beginTransaction')
                 ->once()
                 ->withNoArgs()
                 ->andReturn();
        $this->em->shouldReceive('close')->once()->withNoArgs()->andReturn();
        $this->em->shouldReceive('rollback')->once()->withNoArgs()->andReturn();

        $this->shipmentRepository->expects('find')->with(1)->andReturns(null);

        $this->dataMock->expects('getId')->withNoArgs()->andReturns(1);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage("Shipment not found!");

        $this->sut->update($this->dataMock);
    }

    protected function setDataMock(): void
    {
        $this->dataMock->expects('getStatus')->withNoArgs()->andReturns('WAITING');
        $this->dataMock->expects('getCategory')->withNoArgs()->andReturns("normal");
        $deliveryDate = new DateTime();
        $this->dataMock->expects('getDeliveryDate')->withNoArgs()->andReturns($deliveryDate);

        $this->shipment->expects('setId')->with(1)->andReturnSelf();
        $this->shipment->expects('setDeliveryDate')->with($deliveryDate)->andReturnSelf();
        $this->shipment->expects('setCategory')->with('normal')->andReturnSelf();
        $this->shipment->expects('setStatus')->with('WAITING')->andReturnSelf();
    }
}
