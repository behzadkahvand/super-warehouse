<?php

namespace App\Tests\Unit\Listeners\Integration\Timcheh\GIReceipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ShipmentStatusDictionary;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\Shipment;
use App\Listeners\Integration\Timcheh\GIReceipt\GIReceiptStatusListener;
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

class GIReceiptStatusListenerTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|MessageBusInterface|MockInterface|null $busMock;

    protected LegacyMockInterface|IntegrationablePropertiesDiscoverService|MockInterface|null $discoverMock;

    protected LegacyMockInterface|LifecycleEventArgs|MockInterface|null $eventArgsMock;

    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $emMock;

    protected UnitOfWork|LegacyMockInterface|MockInterface|null $uowMock;

    protected LegacyMockInterface|GIShipmentReceipt|MockInterface|null $GIShipmentReceipt;

    protected ?GIReceiptStatusListener $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->busMock           = Mockery::mock(MessageBusInterface::class);
        $this->discoverMock      = Mockery::mock(IntegrationablePropertiesDiscoverService::class);
        $this->eventArgsMock     = Mockery::mock(LifecycleEventArgs::class);
        $this->emMock            = Mockery::mock(EntityManagerInterface::class);
        $this->uowMock           = Mockery::mock(UnitOfWork::class);
        $this->GIShipmentReceipt = Mockery::mock(GIShipmentReceipt::class);

        $this->sut = new GIReceiptStatusListener($this->busMock, $this->discoverMock, $this->emMock);
    }

    public function testItCanDispatchMessageOnPostUpdate(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->GIShipmentReceipt), ['GIReceipt.status.update'])
                           ->andReturns(["status"]);

        $this->eventArgsMock->expects('getEntityManager')->withNoArgs()->andReturns($this->emMock);

        $this->emMock->expects('getUnitOfWork')->withNoArgs()->andReturns($this->uowMock);

        $this->uowMock->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->uowMock->expects('getEntityChangeSet')
                      ->with($this->GIShipmentReceipt)
                      ->andReturns([
                          "status"    => [
                              0 => ReceiptStatusDictionary::PICKING,
                              1 => ReceiptStatusDictionary::DONE,
                          ],
                          'updatedAt' => [
                              0 => new DateTimeImmutable('08:00:00'),
                              1 => new DateTimeImmutable('11:00:00'),
                          ],
                      ]);

        $shipment = Mockery::mock(Shipment::class);
        $shipment->expects("setStatus")
                 ->with(ShipmentStatusDictionary::PREPARED)
                 ->andReturnSelf();

        $this->GIShipmentReceipt->expects('getReference')->withNoArgs()->andReturns($shipment);

        $this->GIShipmentReceipt->expects('getStatus')
                                ->withNoArgs()
                                ->andReturns(ReceiptStatusDictionary::DONE);

        $this->emMock->expects("flush")->andReturn();

        $this->sut->onPostUpdate($this->GIShipmentReceipt, $this->eventArgsMock);
    }

    public function testItCanDoNothingOnPostUpdateWhenItHasNotIntegrationableProperties(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->GIShipmentReceipt), ['GIReceipt.status.update'])
                           ->andReturns([]);

        $this->sut->onPostUpdate($this->GIShipmentReceipt, $this->eventArgsMock);
    }

    public function testItCanDoNothingOnPostUpdateWhenEntityHasNoChangeSet(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->GIShipmentReceipt), ['GIReceipt.status.update'])
                           ->andReturns(["status",]);

        $this->eventArgsMock->expects('getEntityManager')->withNoArgs()->andReturns($this->emMock);

        $this->emMock->expects('getUnitOfWork')->withNoArgs()->andReturns($this->uowMock);

        $this->uowMock->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->uowMock->expects('getEntityChangeSet')
                      ->with($this->GIShipmentReceipt)
                      ->andReturns([]);

        $this->sut->onPostUpdate($this->GIShipmentReceipt, $this->eventArgsMock);
    }
}
