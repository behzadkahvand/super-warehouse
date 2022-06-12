<?php

namespace App\Tests\Unit\Service\Receipt;

use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\GIShipmentReceiptData;
use App\Entity\Inventory;
use App\Entity\Receipt;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\ReceiptItem;
use App\Entity\Shipment;
use App\Entity\ShipmentItem;
use App\Entity\Warehouse;
use App\Entity\WarehouseStock;
use App\Service\Receipt\GIShipmentReceiptService;
use App\Service\Receipt\ReceiptFactory;
use App\Service\Receipt\ReceiptItemFactory;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Service\WarehouseStock\GIAndSTOutboundWarehouseStockService;
use App\Service\WarehouseStock\GICancelWarehouseStockService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class GIShipmentReceiptServiceTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $em;

    protected LegacyMockInterface|ReceiptItemFactory|MockInterface|null $itemFactoryMock;

    protected ReceiptFactory|LegacyMockInterface|MockInterface|null $factoryMock;

    protected LegacyMockInterface|MockInterface|GIShipmentReceipt|null $receiptMock;

    protected LegacyMockInterface|ReceiptItem|MockInterface|null $receiptItemMock;

    protected LegacyMockInterface|Shipment|MockInterface|null $shipmentMock;

    protected ShipmentItem|LegacyMockInterface|MockInterface|null $shipmentItemMock;

    protected LegacyMockInterface|Warehouse|MockInterface|null $warehouseMock;

    protected LegacyMockInterface|Inventory|MockInterface|null $inventoryMock;

    protected ?GIShipmentReceiptService $shipmentReferenceReceiptService;

    protected GIAndSTOutboundWarehouseStockService|LegacyMockInterface|MockInterface|null $GIAndSTOutboundWarehouseStockService;

    protected StateTransitionHandlerService|LegacyMockInterface|MockInterface|null $transitionService;

    protected GICancelWarehouseStockService|LegacyMockInterface|MockInterface|null $GICancelWarehouseStockService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em                                   = Mockery::mock(EntityManagerInterface::class);
        $this->itemFactoryMock                      = Mockery::mock(ReceiptItemFactory::class);
        $this->factoryMock                          = Mockery::mock(ReceiptFactory::class);
        $this->receiptMock                          = Mockery::mock(GIShipmentReceipt::class);
        $this->receiptItemMock                      = Mockery::mock(ReceiptItem::class);
        $this->shipmentMock                         = Mockery::mock(Shipment::class);
        $this->shipmentItemMock                     = Mockery::mock(ShipmentItem::class);
        $this->warehouseMock                        = Mockery::mock(Warehouse::class);
        $this->inventoryMock                        = Mockery::mock(Inventory::class);
        $this->GIAndSTOutboundWarehouseStockService = Mockery::mock(GIAndSTOutboundWarehouseStockService::class);
        $this->transitionService                    = Mockery::mock(StateTransitionHandlerService::class);
        $this->GICancelWarehouseStockService        = Mockery::mock(GICancelWarehouseStockService::class);

        $this->shipmentReferenceReceiptService = new GIShipmentReceiptService(
            $this->em,
            $this->itemFactoryMock,
            $this->factoryMock,
            $this->GIAndSTOutboundWarehouseStockService,
            $this->transitionService,
            $this->GICancelWarehouseStockService
        );
    }

    public function testItCanCreateShipmentReferenceReceiptWithReservedStatus(): void
    {
        $receiptData = new GIShipmentReceiptData();

        $receiptData->setShipment($this->shipmentMock)
                    ->setWarehouse($this->warehouseMock);

        $this->factoryMock->expects('create')
                          ->with(ReceiptReferenceTypeDictionary::GI_SHIPMENT)
                          ->andReturns($this->receiptMock);

        $this->receiptMock->expects('setStatus')
                          ->with(ReceiptStatusDictionary::RESERVED)
                          ->andReturnSelf();
        $this->receiptMock->expects('setSourceWarehouse')
                          ->with($this->warehouseMock)
                          ->andReturnSelf();
        $this->receiptMock->expects('setReference')
                          ->with($this->shipmentMock)
                          ->andReturnSelf();
        $this->receiptMock->expects('getReference')
                          ->withNoArgs()
                          ->andReturns($this->shipmentMock);

        $this->shipmentMock->expects('getShipmentItems')
                           ->withNoArgs()
                           ->andReturns(new ArrayCollection([$this->shipmentItemMock, $this->shipmentItemMock]));

        $this->shipmentItemMock->shouldReceive('getQuantity')
                               ->twice()
                               ->withNoArgs()
                               ->andReturns(2, 3);
        $this->shipmentItemMock->shouldReceive('getInventory')
                               ->twice()
                               ->withNoArgs()
                               ->andReturns($this->inventoryMock);

        $this->shipmentItemMock->shouldReceive('setReceiptItem')
                               ->twice()
                               ->with($this->receiptItemMock)
                               ->andReturnSelf();

        $this->itemFactoryMock->shouldReceive('create')
                              ->twice()
                              ->withNoArgs()
                              ->andReturns($this->receiptItemMock);

        $this->receiptItemMock->shouldReceive('setStatus')
                              ->twice()
                              ->with(ReceiptStatusDictionary::RESERVED)
                              ->andReturnSelf();
        $this->receiptItemMock->expects('setQuantity')
                              ->once()
                              ->with(2)
                              ->andReturnSelf();
        $this->receiptItemMock->expects('setQuantity')
                              ->with(3)
                              ->andReturnSelf();
        $this->receiptItemMock->shouldReceive('setInventory')
                              ->twice()
                              ->with($this->inventoryMock)
                              ->andReturnSelf();

        $this->receiptMock->shouldReceive('addReceiptItem')
                          ->twice()
                          ->with($this->receiptItemMock)
                          ->andReturnSelf();

        $this->GIAndSTOutboundWarehouseStockService->shouldReceive('depositReservedAndSupplyStock')
                                                   ->twice()
                                                   ->with($this->receiptItemMock)
                                                   ->andReturn(Mockery::mock(WarehouseStock::class));

        $this->em->shouldReceive('persist')
                 ->twice()
                 ->with($this->receiptItemMock)
                 ->andReturns();

        $this->em->expects('persist')
                 ->with($this->receiptMock)
                 ->andReturns();
        $this->em->expects('flush')
                 ->withNoArgs()
                 ->andReturns();

        $result = $this->shipmentReferenceReceiptService->create($receiptData);

        self::assertInstanceOf(Receipt::class, $result);
    }

    public function testItCanCancelShipment(): void
    {
        $shipment     = Mockery::mock(Shipment::class);
        $shipmentItem = Mockery::mock(ShipmentItem::class);

        $receiptItem  = Mockery::mock(ReceiptItem::class);
        $shipmentItem->shouldReceive('getReceiptItem')
                     ->twice()
                     ->withNoArgs()
                     ->andReturns($receiptItem);

        $items = [
            $shipmentItem,
            $shipmentItem,
        ];
        $shipment->expects('getShipmentItems')->withNoArgs()->andReturns(new ArrayCollection($items));

        $this->transitionService->shouldReceive('transitState')
                                ->with($receiptItem, ReceiptStatusDictionary::CANCELED)
                                ->twice()
                                ->andReturn();

        $this->GICancelWarehouseStockService->shouldReceive('withdrawReserveAndSupplyStock')
                                ->with($shipmentItem)
                                ->twice()
                                ->andReturn();

        $this->shipmentReferenceReceiptService->cancel($shipment);
    }
}
