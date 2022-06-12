<?php

namespace App\Tests\Unit\Listeners\Integration\Timcheh\SellerPackage;

use App\Dictionary\SellerPackageStatusDictionary;
use App\Entity\SellerPackage;
use App\Listeners\Integration\Timcheh\SellerPackage\SellerPackageIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\SellerPackage\UpdateSellerPackageMessage;
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

class SellerPackageIntegrationListenerTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|MessageBusInterface|MockInterface|null $busMock;

    protected LegacyMockInterface|IntegrationablePropertiesDiscoverService|MockInterface|null $discoverMock;

    protected LegacyMockInterface|LifecycleEventArgs|MockInterface|null $eventArgsMock;

    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $emMock;

    protected UnitOfWork|LegacyMockInterface|MockInterface|null $uowMock;

    protected LegacyMockInterface|SellerPackage|MockInterface|null $packageMock;

    protected ?SellerPackageIntegrationListener $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->busMock       = Mockery::mock(MessageBusInterface::class);
        $this->discoverMock  = Mockery::mock(IntegrationablePropertiesDiscoverService::class);
        $this->eventArgsMock = Mockery::mock(LifecycleEventArgs::class);
        $this->emMock        = Mockery::mock(EntityManagerInterface::class);
        $this->uowMock       = Mockery::mock(UnitOfWork::class);
        $this->packageMock   = Mockery::mock(SellerPackage::class);

        $this->sut = new SellerPackageIntegrationListener($this->busMock, $this->discoverMock);
    }

    public function testItCanDispatchMessageOnPostUpdate(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->packageMock), ['timcheh.seller-package.update'])
                           ->andReturns(["status",]);

        $this->eventArgsMock->expects('getEntityManager')->withNoArgs()->andReturns($this->emMock);

        $this->emMock->expects('getUnitOfWork')->withNoArgs()->andReturns($this->uowMock);

        $this->uowMock->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->uowMock->expects('getEntityChangeSet')
                      ->with($this->packageMock)
                      ->andReturns([
                          "status" => [
                              0 => SellerPackageStatusDictionary::SENT,
                              1 => SellerPackageStatusDictionary::RECEIVED
                          ],
                          'updatedAt' => [
                              0 => new DateTimeImmutable('08:00:00'),
                              1 => new DateTimeImmutable('11:00:00'),
                          ]
                      ]);

        $this->packageMock->expects('getId')->withNoArgs()->andReturns(1);
        $this->packageMock->expects('getStatus')
                          ->withNoArgs()
                          ->andReturns(SellerPackageStatusDictionary::RECEIVED);

        $this->busMock->expects('dispatch')
                      ->with(Mockery::type(UpdateSellerPackageMessage::class))
                      ->andReturns();

        $this->sut->onPostUpdate($this->packageMock, $this->eventArgsMock);
    }

    public function testItCanDoNothingOnPostUpdateWhenItHasNotIntegrationableProperties(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->packageMock), ['timcheh.seller-package.update'])
                           ->andReturns([]);

        $this->sut->onPostUpdate($this->packageMock, $this->eventArgsMock);
    }

    public function testItCanDoNothingOnPostUpdateWhenEntityHasNoChangeSet(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->packageMock), ['timcheh.seller-package.update'])
                           ->andReturns(["status",]);

        $this->eventArgsMock->expects('getEntityManager')->withNoArgs()->andReturns($this->emMock);

        $this->emMock->expects('getUnitOfWork')->withNoArgs()->andReturns($this->uowMock);

        $this->uowMock->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->uowMock->expects('getEntityChangeSet')
                      ->with($this->packageMock)
                      ->andReturns([]);

        $this->sut->onPostUpdate($this->packageMock, $this->eventArgsMock);
    }
}
