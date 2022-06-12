<?php

namespace App\Tests\Unit\Service\Shipment\Integration;

use App\Entity\ReceiptItem;
use App\Entity\ShipmentItem;
use App\Entity\WarehouseStock;
use App\Repository\ShipmentItemRepository;
use App\Service\Shipment\Integration\ShipmentItemUpdateService;
use App\Service\WarehouseStock\GIUpdateWarehouseStockService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Mockery;

final class ShipmentItemUpdateServiceTest extends BaseUnitTestCase
{
    protected ?ShipmentItemUpdateService $shipmentItemUpdateService;

    protected ?ShipmentItemRepository $repository;

    protected ?EntityManagerInterface $manager;

    protected ?GIUpdateWarehouseStockService $GIUpdateWarehouseStockService;

    protected ?ShipmentItem $shipmentItem;

    protected ?ReceiptItem $receiptItem;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(ShipmentItemRepository::class);
        $this->manager = Mockery::mock(EntityManagerInterface::class);
        $this->GIUpdateWarehouseStockService = Mockery::mock(GIUpdateWarehouseStockService::class);
        $this->shipmentItem = Mockery::mock(ShipmentItem::class);
        $this->receiptItem = Mockery::mock(ReceiptItem::class);

        $this->shipmentItemUpdateService = new ShipmentItemUpdateService(
            $this->repository,
            $this->manager,
            $this->GIUpdateWarehouseStockService
        );
    }

    public function testItThrowExceptionWhenShipmentItemNotFound(): void
    {
        $this->repository->expects('find')
            ->with(1)
            ->andReturnNull();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('shipment item not found!');

        $this->shipmentItemUpdateService->update(1, 1);
    }

    public function testItThrowExceptionWhenWithdrawStock(): void
    {
        $this->repository->expects('find')
            ->with(1)
            ->andReturn($this->shipmentItem);

        $this->shipmentItem->expects('getQuantity')
            ->withNoArgs()
            ->andReturn(2);

        $this->GIUpdateWarehouseStockService->expects('withdrawReserveAndSupplyStock')
            ->with($this->shipmentItem, 1)
            ->andThrow(Exception::class);

        $this->manager->expects('beginTransaction')
            ->withNoArgs()
            ->andReturns();

        $this->manager->expects('close')
            ->withNoArgs()
            ->andReturns();

        $this->manager->expects('rollback')
            ->withNoArgs()
            ->andReturns();

        $this->expectException(Exception::class);

        $this->shipmentItemUpdateService->update(1, 1);
    }

    public function testItUpdateShipmentItemSuccessfullyWhenQuantityDecrease(): void
    {
        $this->repository->expects('find')
            ->with(1)
            ->andReturn($this->shipmentItem);

        $this->shipmentItem->expects('getQuantity')
            ->withNoArgs()
            ->andReturn(2);

        $this->GIUpdateWarehouseStockService->expects('withdrawReserveAndSupplyStock')
            ->with($this->shipmentItem, 1)
            ->andReturn(new WarehouseStock());

        $this->manager->expects('beginTransaction')
            ->withNoArgs()
            ->andReturns();

        $this->shipmentItem->expects('setQuantity')
            ->with(1)
            ->andReturnSelf();

        $this->shipmentItem->expects('getReceiptItem')
            ->withNoArgs()
            ->andReturn($this->receiptItem);

        $this->receiptItem->expects('setQuantity')
            ->with(1)
            ->andReturnSelf();

        $this->manager->expects('flush')
            ->withNoArgs()
            ->andReturns();

        $this->manager->expects('commit')
            ->withNoArgs()
            ->andReturns();

        $this->shipmentItemUpdateService->update(1, 1);
    }

    public function testItUpdateShipmentItemSuccessfullyWhenQuantityIncrease(): void
    {
        $this->repository->expects('find')
            ->with(1)
            ->andReturn($this->shipmentItem);

        $this->shipmentItem->expects('getQuantity')
            ->withNoArgs()
            ->andReturn(2);

        $this->GIUpdateWarehouseStockService->expects('depositReserveAndSupplyStock')
            ->with($this->shipmentItem, 1)
            ->andReturn(new WarehouseStock());

        $this->manager->expects('beginTransaction')
            ->withNoArgs()
            ->andReturns();

        $this->shipmentItem->expects('setQuantity')
            ->with(3)
            ->andReturnSelf();

        $this->shipmentItem->expects('getReceiptItem')
            ->withNoArgs()
            ->andReturn($this->receiptItem);

        $this->receiptItem->expects('setQuantity')
            ->with(3)
            ->andReturnSelf();

        $this->manager->expects('flush')
            ->withNoArgs()
            ->andReturns();

        $this->manager->expects('commit')
            ->withNoArgs()
            ->andReturns();

        $this->shipmentItemUpdateService->update(1, 3);
    }
}
