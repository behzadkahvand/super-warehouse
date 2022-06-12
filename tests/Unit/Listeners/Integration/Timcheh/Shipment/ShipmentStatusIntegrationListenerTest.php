<?php

namespace App\Tests\Unit\Listeners\Integration\Timcheh\Shipment;

use App\Dictionary\ShipmentStatusDictionary;
use App\Entity\Shipment;
use App\Listeners\Integration\Timcheh\Shipment\ShipmentStatusIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\UpdateShipmentStatusMessage;
use App\Service\Integration\IntegrationablePropertiesDiscoverService;
use App\Tests\Unit\BaseUnitTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ShipmentStatusIntegrationListenerTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|MessageBusInterface|MockInterface|null $busMock;

    protected LegacyMockInterface|IntegrationablePropertiesDiscoverService|MockInterface|null $discoverMock;

    protected LegacyMockInterface|LifecycleEventArgs|MockInterface|null $eventArgsMock;

    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $emMock;

    protected UnitOfWork|LegacyMockInterface|MockInterface|null $uowMock;

    protected LegacyMockInterface|Shipment|MockInterface|null $shipment;

    protected ?ShipmentStatusIntegrationListener $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->busMock       = Mockery::mock(MessageBusInterface::class);
        $this->discoverMock  = Mockery::mock(IntegrationablePropertiesDiscoverService::class);
        $this->eventArgsMock = Mockery::mock(LifecycleEventArgs::class);
        $this->emMock        = Mockery::mock(EntityManagerInterface::class);
        $this->uowMock       = Mockery::mock(UnitOfWork::class);
        $this->shipment      = Mockery::mock(Shipment::class);

        $this->sut = new ShipmentStatusIntegrationListener($this->busMock, $this->discoverMock);
    }

    public function testItCanDispatchMessageOnPostUpdate(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->shipment), ['timcheh.shipment.status.update'])
                           ->andReturns(["status"]);

        $this->eventArgsMock->expects('getEntityManager')->withNoArgs()->andReturns($this->emMock);

        $this->emMock->expects('getUnitOfWork')->withNoArgs()->andReturns($this->uowMock);

        $this->uowMock->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->uowMock->expects('getEntityChangeSet')
                      ->with($this->shipment)
                      ->andReturns([
                          "status"    => [
                              0 => ShipmentStatusDictionary::PREPARING,
                              1 => ShipmentStatusDictionary::PREPARED,
                          ],
                          'updatedAt' => [
                              0 => new DateTimeImmutable('08:00:00'),
                              1 => new DateTimeImmutable('11:00:00'),
                          ],
                      ]);

        $this->shipment->expects('getId')->withNoArgs()->andReturns(1);
        $this->shipment->expects('getStatus')
                       ->withNoArgs()
                       ->twice()
                       ->andReturns(ShipmentStatusDictionary::PREPARED);

        $this->busMock->expects('dispatch')
                      ->with(Mockery::type(UpdateShipmentStatusMessage::class))
                      ->andReturns();

        $this->sut->onPostUpdate($this->shipment, $this->eventArgsMock);
    }

    public function testItCanDoNothingOnPostUpdateWhenItHasNotIntegrationableProperties(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->shipment), ['timcheh.shipment.status.update'])
                           ->andReturns([]);

        $this->sut->onPostUpdate($this->shipment, $this->eventArgsMock);
    }

    public function testItCanDoNothingOnPostUpdateWhenEntityHasNoChangeSet(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->shipment), ['timcheh.shipment.status.update'])
                           ->andReturns(["status",]);

        $this->eventArgsMock->expects('getEntityManager')->withNoArgs()->andReturns($this->emMock);

        $this->emMock->expects('getUnitOfWork')->withNoArgs()->andReturns($this->uowMock);

        $this->uowMock->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->uowMock->expects('getEntityChangeSet')
                      ->with($this->shipment)
                      ->andReturns([]);

        $this->sut->onPostUpdate($this->shipment, $this->eventArgsMock);
    }
}
