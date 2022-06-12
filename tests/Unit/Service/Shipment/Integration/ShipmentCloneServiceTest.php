<?php

namespace App\Tests\Unit\Service\Shipment\Integration;

use App\Entity\Shipment;
use App\Entity\ShipmentItem;
use App\Repository\ShipmentItemRepository;
use App\Repository\ShipmentRepository;
use App\Service\Shipment\DTO\ShipmentCloneData;
use App\Service\Shipment\DTO\ShipmentData;
use App\Service\Shipment\Integration\ShipmentCloneService;
use App\Service\Shipment\ShipmentUpsertService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Mockery;

class ShipmentCloneServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|EntityManagerInterface|Mockery\MockInterface|null $manager;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|ShipmentRepository|null $shipmentRepository;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|ShipmentItemRepository|null $shipmentItemRepository;

    protected ShipmentUpsertService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $shipmentUpsertService;

    protected ShipmentCloneService|null $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager                = Mockery::mock(EntityManagerInterface::class);
        $this->shipmentRepository     = Mockery::mock(ShipmentRepository::class);
        $this->shipmentItemRepository = Mockery::mock(ShipmentItemRepository::class);
        $this->shipmentUpsertService  = Mockery::mock(ShipmentUpsertService::class);

        $this->sut = new ShipmentCloneService(
            $this->manager,
            $this->shipmentRepository,
            $this->shipmentItemRepository,
            $this->shipmentUpsertService
        );
    }

    public function testItCanCloneWhenTargetShipmentIsNew(): void
    {
        $cloneData = (new ShipmentCloneData())
            ->setSourceShipment([
                'id'           => 1,
                'status'       => 'NEW',
                'category'     => 'test',
                'deliveryDate' => '2022-01-20T00:00:00+03:30',
                'items'        => [['id' => 1, 'inventoryId' => 5, 'quantity' => 3, 'stockType' => 'SELLER']],
            ])
            ->setTargetShipment([
                'id'           => 2,
                'status'       => 'NEW',
                'category'     => 'test2',
                'deliveryDate' => '2022-01-21T00:00:00+03:30',
                'items'        => [['id' => 2, 'inventoryId' => 4, 'quantity' => 1, 'stockType' => 'SELLER']],
            ]);

        $sourceShipment = Mockery::mock(Shipment::class);

        $this->shipmentRepository->expects('find')
                                 ->with(1)
                                 ->andReturn($sourceShipment);

        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturns();

        $this->shipmentRepository->expects('find')
                                 ->with(2)
                                 ->andReturnNull();

        $targetShipment = Mockery::mock(Shipment::class);

        $this->shipmentUpsertService->expects('create')
                                    ->with(Mockery::type(ShipmentData::class))
                                    ->andReturn($targetShipment);

        $shipmentItem = Mockery::mock(ShipmentItem::class);

        $this->shipmentItemRepository->expects('find')
                                     ->with(2)
                                     ->andReturn($shipmentItem);

        $this->shipmentUpsertService->expects('updateShipmentItems')
                                    ->with($targetShipment, [$shipmentItem])
                                    ->andReturn();

        $this->manager->expects('flush')
                      ->withNoArgs()
                      ->andReturns();

        $this->manager->expects('commit')
                      ->withNoArgs()
                      ->andReturns();

        ($this->sut)($cloneData);
    }

    public function testItCanCloneWhenTargetShipmentAlreadyExistsAndSourceShipmentDoesNotHaveAnyItem(): void
    {
        $cloneData = (new ShipmentCloneData())
            ->setSourceShipment([
                'id'           => 1,
                'status'       => 'NEW',
                'category'     => 'test',
                'deliveryDate' => '2022-01-20T00:00:00+03:30',
                'items'        => [],
            ])
            ->setTargetShipment([
                'id'           => 2,
                'status'       => 'NEW',
                'category'     => 'test2',
                'deliveryDate' => '2022-01-21T00:00:00+03:30',
                'items'        => [
                    ['id' => 1, 'inventoryId' => 5, 'quantity' => 3, 'stockType' => 'SELLER'],
                    ['id' => 2, 'inventoryId' => 4, 'quantity' => 1, 'stockType' => 'SELLER'],
                ],
            ]);

        $sourceShipment = Mockery::mock(Shipment::class);

        $this->shipmentRepository->expects('find')
                                 ->with(1)
                                 ->andReturn($sourceShipment);

        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturns();

        $targetShipment = Mockery::mock(Shipment::class);

        $this->shipmentRepository->expects('find')
                                 ->with(2)
                                 ->andReturn($targetShipment);

        $shipmentItemOld = Mockery::mock(ShipmentItem::class);
        $shipmentItemOld->expects('getId')
                        ->withNoArgs()
                        ->andReturn(1);

        $targetShipment->expects('getShipmentItems')
                       ->withNoArgs()
                       ->andReturn(new ArrayCollection([$shipmentItemOld]));

        $shipmentItemNew = Mockery::mock(ShipmentItem::class);

        $this->shipmentItemRepository->expects('find')
                                     ->with(2)
                                     ->andReturn($shipmentItemNew);

        $this->shipmentUpsertService->expects('updateShipmentItems')
                                    ->with($targetShipment, [$shipmentItemNew])
                                    ->andReturn();

        $this->manager->expects('remove')
                      ->with($sourceShipment)
                      ->andReturns();

        $this->manager->expects('flush')
                      ->withNoArgs()
                      ->andReturns();

        $this->manager->expects('commit')
                      ->withNoArgs()
                      ->andReturns();

        ($this->sut)($cloneData);
    }

    public function testCloneFailedWhenSourceShipmentNotFound(): void
    {
        $cloneData = (new ShipmentCloneData())
            ->setSourceShipment([
                'id'           => 1,
                'status'       => 'NEW',
                'category'     => 'test',
                'deliveryDate' => '2022-01-20T00:00:00+03:30',
                'items'        => [['id' => 1, 'inventoryId' => 5, 'quantity' => 3, 'stockType' => 'SELLER']],
            ])
            ->setTargetShipment([
                'id'           => 2,
                'status'       => 'NEW',
                'category'     => 'test2',
                'deliveryDate' => '2022-01-21T00:00:00+03:30',
                'items'        => [['id' => 2, 'inventoryId' => 4, 'quantity' => 1, 'stockType' => 'SELLER']],
            ]);

        $this->shipmentRepository->expects('find')
                                 ->with(1)
                                 ->andReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Source shipment not found!');

        ($this->sut)($cloneData);
    }

    public function testCloneFailedWhenNewItemNotFound(): void
    {
        $cloneData = (new ShipmentCloneData())
            ->setSourceShipment([
                'id'           => 1,
                'status'       => 'NEW',
                'category'     => 'test',
                'deliveryDate' => '2022-01-20T00:00:00+03:30',
                'items'        => [],
            ])
            ->setTargetShipment([
                'id'           => 2,
                'status'       => 'NEW',
                'category'     => 'test2',
                'deliveryDate' => '2022-01-21T00:00:00+03:30',
                'items'        => [
                    ['id' => 1, 'inventoryId' => 5, 'quantity' => 3, 'stockType' => 'SELLER'],
                    ['id' => 2, 'inventoryId' => 4, 'quantity' => 1, 'stockType' => 'SELLER'],
                ],
            ]);

        $sourceShipment = Mockery::mock(Shipment::class);

        $this->shipmentRepository->expects('find')
                                 ->with(1)
                                 ->andReturn($sourceShipment);

        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturns();

        $targetShipment = Mockery::mock(Shipment::class);

        $this->shipmentRepository->expects('find')
                                 ->with(2)
                                 ->andReturn($targetShipment);

        $shipmentItemOld = Mockery::mock(ShipmentItem::class);
        $shipmentItemOld->expects('getId')
                        ->withNoArgs()
                        ->andReturn(1);

        $targetShipment->expects('getShipmentItems')
                       ->withNoArgs()
                       ->andReturn(new ArrayCollection([$shipmentItemOld]));

        $this->shipmentItemRepository->expects('find')
                                     ->with(2)
                                     ->andReturn(null);

        $this->manager->expects('close')
                      ->withNoArgs()
                      ->andReturns();

        $this->manager->expects('rollback')
                      ->withNoArgs()
                      ->andReturns();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Shipment item not found!');

        ($this->sut)($cloneData);
    }
}
