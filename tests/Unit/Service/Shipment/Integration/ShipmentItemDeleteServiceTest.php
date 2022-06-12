<?php

namespace App\Tests\Unit\Service\Shipment\Integration;

use App\Entity\ReceiptItem;
use App\Entity\ShipmentItem;
use App\Entity\WarehouseStock;
use App\Repository\ShipmentItemRepository;
use App\Service\Shipment\Integration\ShipmentItemDeleteService;
use App\Service\WarehouseStock\GICancelWarehouseStockService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Mockery;

final class ShipmentItemDeleteServiceTest extends BaseUnitTestCase
{
    protected ?ShipmentItemDeleteService $shipmentItemDeleteService;

    protected ?ShipmentItemRepository $repository;

    protected ?EntityManagerInterface $manager;

    protected ?GICancelWarehouseStockService $GICancelWarehouseStockService;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(ShipmentItemRepository::class);
        $this->manager = Mockery::mock(EntityManagerInterface::class);
        $this->GICancelWarehouseStockService = Mockery::mock(GICancelWarehouseStockService::class);

        $this->shipmentItemDeleteService = new ShipmentItemDeleteService(
            $this->repository,
            $this->manager,
            $this->GICancelWarehouseStockService
        );
    }

    public function testItThrowExceptionWhenShipmentItemNotFound(): void
    {
        $this->repository->expects('find')
            ->with(1)
            ->andReturnNull();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('shipment item not found!');

        $this->shipmentItemDeleteService->delete(1);
    }

    public function testItThrowExceptionWhenWithdrawStock(): void
    {
        $this->repository->expects('find')
            ->with(1)
            ->andReturn(new ShipmentItem());

        $this->GICancelWarehouseStockService->expects('withdrawReserveAndSupplyStock')
            ->with(Mockery::type(ShipmentItem::class))
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

        $this->shipmentItemDeleteService->delete(1);
    }

    public function testItDeleteShipmentItemSuccessfully(): void
    {
        $shipmentItem = Mockery::mock(ShipmentItem::class);

        $shipmentItem->expects('getReceiptItem')
            ->withNoArgs()
            ->andReturn(new ReceiptItem());

        $this->repository->expects('find')
            ->with(1)
            ->andReturn($shipmentItem);

        $this->GICancelWarehouseStockService->expects('withdrawReserveAndSupplyStock')
            ->with(Mockery::type(ShipmentItem::class))
            ->andReturn(new WarehouseStock());

        $this->manager->expects('beginTransaction')
            ->withNoArgs()
            ->andReturns();

        $this->manager->expects('remove')
            ->with(Mockery::type(ReceiptItem::class))
            ->andReturns();

        $this->manager->expects('remove')
            ->with(Mockery::type(ShipmentItem::class))
            ->andReturns();

        $this->manager->expects('flush')
            ->withNoArgs()
            ->andReturns();

        $this->manager->expects('commit')
            ->withNoArgs()
            ->andReturns();

        $this->shipmentItemDeleteService->delete(1);
    }
}
