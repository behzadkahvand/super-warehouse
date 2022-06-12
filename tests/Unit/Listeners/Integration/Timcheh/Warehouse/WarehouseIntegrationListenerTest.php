<?php

namespace App\Tests\Unit\Listeners\Integration\Timcheh\Warehouse;

use App\Entity\Warehouse;
use App\Listeners\Integration\Timcheh\Warehouse\WarehouseIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\Warehouse\CreateWarehouseMessage;
use App\Messaging\Messages\Event\Integration\Timcheh\Warehouse\UpdateWarehouseMessage;
use App\Service\Integration\IntegrationablePropertiesDiscoverService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use Mockery;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class WarehouseIntegrationListenerTest extends BaseUnitTestCase
{
    protected ?Warehouse $warehouse;

    protected ?MessageBusInterface $bus;

    private ?IntegrationablePropertiesDiscoverService $discoverService;

    private ?LifecycleEventArgs $lifecycleEventArgs;

    private ?EntityManagerInterface $manager;

    private ?UnitOfWork $unitOfWork;

    private ?WarehouseIntegrationListener $listener;

    public function setUp(): void
    {
        parent::setUp();

        $this->warehouse = Mockery::mock(Warehouse::class);
        $this->bus = Mockery::mock(MessageBusInterface::class);
        $this->discoverService = Mockery::mock(IntegrationablePropertiesDiscoverService::class);
        $this->lifecycleEventArgs = Mockery::mock(LifecycleEventArgs::class);
        $this->manager = Mockery::mock(EntityManagerInterface::class);
        $this->unitOfWork = Mockery::mock(UnitOfWork::class);
        $this->listener = new WarehouseIntegrationListener($this->bus, $this->discoverService);
    }

    public function testDoPostPersist(): void
    {
        $this->callWarehouseMockExpectations();

        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->warehouse), ['timcheh.warehouse.update'])
            ->andReturns(['id', 'title', 'isActive', 'address', 'coordinates', 'forFmcgMarketPlacePurchase']);

        $this->bus->expects('dispatch')
            ->with(Mockery::type(CreateWarehouseMessage::class))
            ->andReturn(new Envelope(new \stdClass()));

        $this->listener->onPostPersist($this->warehouse);
    }

    public function testDispatchMessageForUpdatingWarehouse(): void
    {
        $this->callWarehouseMockExpectations();

        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->warehouse), ['timcheh.warehouse.update'])
            ->andReturns(['id', 'title', 'isActive', 'address', 'coordinates', 'forFmcgMarketPlacePurchase']);

        $this->lifecycleEventArgs->expects('getEntityManager')
            ->withNoArgs()
            ->andReturns($this->manager);

        $this->bus->expects('dispatch')
            ->with(Mockery::type(UpdateWarehouseMessage::class))
            ->andReturn(new Envelope(new \stdClass()));

        $this->manager->expects('getUnitOfWork')->withNoArgs()->andReturns($this->unitOfWork);

        $this->unitOfWork->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->unitOfWork->expects('getEntityChangeSet')
            ->with($this->warehouse)
            ->andReturns([
                             "title" => [
                                 0 => 'test1',
                                 1 => 'test2'
                             ],
                             'address' => [
                                 0 => 'address1',
                                 1 => 'address2',
                             ]
                         ]);

        $this->listener->onPostUpdate($this->warehouse, $this->lifecycleEventArgs);
    }

    public function testOnPostUpdateWithEmptyIntegrationableProperties(): void
    {
        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->warehouse), ['timcheh.warehouse.update'])
            ->andReturns([]);

        $this->listener->onPostUpdate($this->warehouse, $this->lifecycleEventArgs);
    }

    public function testOnPostUpdateWithNotChangedIntegrationableProperties(): void
    {
        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->warehouse), ['timcheh.warehouse.update'])
            ->andReturns(["strategyType", "pickingType",]);

        $this->lifecycleEventArgs->expects('getEntityManager')->withNoArgs()->andReturns($this->manager);

        $this->manager->expects('getUnitOfWork')->withNoArgs()->andReturns($this->unitOfWork);

        $this->unitOfWork->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->unitOfWork->expects('getEntityChangeSet')
            ->with($this->warehouse)
            ->andReturns([]);

        $this->listener->onPostUpdate($this->warehouse, $this->lifecycleEventArgs);
    }

    private function callWarehouseMockExpectations(): void
    {
        $this->warehouse->expects('getId')
            ->withNoArgs()
            ->andReturn(1);
        $this->warehouse->expects('getTitle')
            ->withNoArgs()
            ->andReturn('test');
        $this->warehouse->expects('getIsActive')
            ->withNoArgs()
            ->andReturn(1);
        $this->warehouse->expects('getAddress')
            ->withNoArgs()
            ->andReturn('test');
        $this->warehouse->expects('getCoordinates')
            ->withNoArgs()
            ->andReturn(new Point(13.13, 12.12));
        $this->warehouse->expects('getForFmcgMarketPlacePurchase')
            ->withNoArgs()
            ->andReturn(1);
        $this->warehouse->expects('getForMarketPlacePurchase')
            ->withNoArgs()
            ->andReturn(1);
        $this->warehouse->expects('getForRetailPurchase')
            ->withNoArgs()
            ->andReturn(1);
        $this->warehouse->expects('getForSale')
            ->withNoArgs()
            ->andReturn(1);
        $this->warehouse->expects('getForSalesReturn')
            ->withNoArgs()
            ->andReturn(1);
        $this->warehouse->expects('getPhone')
            ->withNoArgs()
            ->andReturn('09121234567');
    }
}
