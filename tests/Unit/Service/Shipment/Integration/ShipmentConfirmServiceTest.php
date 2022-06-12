<?php

namespace App\Tests\Unit\Service\Shipment\Integration;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ShipmentItemStockTypeDictionary;
use App\Entity\ReceiptItem;
use App\Entity\Shipment;
use App\Entity\ShipmentItem;
use App\Repository\ShipmentRepository;
use App\Service\Shipment\Integration\ShipmentConfirmService;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Mockery;

class ShipmentConfirmServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|EntityManagerInterface|Mockery\MockInterface|null $manager;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|ShipmentRepository|null $shipmentRepository;

    protected ShipmentConfirmService|null $sut;

    protected StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $stateTransitionHandlerService;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager                       = Mockery::mock(EntityManagerInterface::class);
        $this->shipmentRepository            = Mockery::mock(ShipmentRepository::class);
        $this->stateTransitionHandlerService = Mockery::mock(StateTransitionHandlerService::class);

        $this->sut = new ShipmentConfirmService(
            $this->manager,
            $this->shipmentRepository,
            $this->stateTransitionHandlerService
        );
    }

    public function testItCanInvokeWhenStatusISWaitingForSupply(): void
    {
        $shipment = Mockery::mock(Shipment::class);

        $this->shipmentRepository->expects('find')
                                 ->with(1)
                                 ->andReturn($shipment);

        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturns();

        $shipmentItem = Mockery::mock(ShipmentItem::class);

        $shipment->expects('getShipmentItems')
                 ->withNoArgs()
                 ->andReturns(new ArrayCollection([$shipmentItem]));

        $shipmentItem->expects('getStockType')
                     ->withNoArgs()
                     ->andReturns(ShipmentItemStockTypeDictionary::SELLER);

        $receiptItem = Mockery::mock(ReceiptItem::class);

        $shipmentItem->expects('getReceiptItem')
                     ->withNoArgs()
                     ->andReturns($receiptItem);

        $this->stateTransitionHandlerService->expects('transitState')
                                            ->with($receiptItem, ReceiptStatusDictionary::WAITING_FOR_SUPPLY)
                                            ->andReturns();

        $this->manager->expects('flush')
                      ->withNoArgs()
                      ->andReturns();

        $this->manager->expects('commit')
                      ->withNoArgs()
                      ->andReturns();

        ($this->sut)(1);
    }

    public function testItCanInvokeWhenStatusISApprove(): void
    {
        $shipment = Mockery::mock(Shipment::class);

        $this->shipmentRepository->expects('find')
                                 ->with(1)
                                 ->andReturn($shipment);

        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturns();

        $shipmentItem = Mockery::mock(ShipmentItem::class);

        $shipment->expects('getShipmentItems')
                 ->withNoArgs()
                 ->andReturns(new ArrayCollection([$shipmentItem]));

        $shipmentItem->expects('getStockType')
                     ->withNoArgs()
                     ->andReturns(ShipmentItemStockTypeDictionary::SALEABLE);

        $receiptItem = Mockery::mock(ReceiptItem::class);

        $shipmentItem->expects('getReceiptItem')
                     ->withNoArgs()
                     ->andReturns($receiptItem);

        $this->stateTransitionHandlerService->expects('transitState')
                                            ->with($receiptItem, ReceiptStatusDictionary::APPROVED)
                                            ->andReturns();

        $this->manager->expects('flush')
                      ->withNoArgs()
                      ->andReturns();

        $this->manager->expects('commit')
                      ->withNoArgs()
                      ->andReturns();

        ($this->sut)(1);
    }

    public function testInvokeFailedWhenShipmentNotFound(): void
    {
        $this->shipmentRepository->expects('find')
                                 ->with(1)
                                 ->andReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Shipment not found!');

        ($this->sut)(1);
    }

    public function testRollbackWhenException(): void
    {
        $shipment = Mockery::mock(Shipment::class);

        $this->shipmentRepository->expects('find')
                                 ->with(1)
                                 ->andReturn($shipment);

        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturns();

        $shipmentItem = Mockery::mock(ShipmentItem::class);

        $shipment->expects('getShipmentItems')
                 ->withNoArgs()
                 ->andReturns(new ArrayCollection([$shipmentItem]));

        $shipmentItem->expects('getStockType')
                     ->withNoArgs()
                     ->andReturns(ShipmentItemStockTypeDictionary::SELLER);

        $receiptItem = Mockery::mock(ReceiptItem::class);

        $shipmentItem->expects('getReceiptItem')
                     ->withNoArgs()
                     ->andReturns($receiptItem);

        $this->stateTransitionHandlerService->expects('transitState')
                                            ->with($receiptItem, ReceiptStatusDictionary::WAITING_FOR_SUPPLY)
                                            ->andThrow(new Exception("test"));

        $this->manager->expects('close')
                      ->withNoArgs()
                      ->andReturns();

        $this->manager->expects('rollback')
                      ->withNoArgs()
                      ->andReturns();

        $this->expectException(Exception::class);

        ($this->sut)(1);
    }
}
