<?php

namespace App\Tests\Unit\Listeners\Integration\Timcheh\SellerPackageItem;

use App\Dictionary\SellerPackageStatusDictionary;
use App\Entity\SellerPackageItem;
use App\Listeners\Integration\Timcheh\SellerPackageItem\SellerPackageItemIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\SellerPackageItem\UpdateSellerPackageItemMessage;
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

class SellerPackageItemIntegrationListenerTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|MessageBusInterface|MockInterface|null $busMock;

    protected LegacyMockInterface|IntegrationablePropertiesDiscoverService|MockInterface|null $discoverMock;

    protected LegacyMockInterface|LifecycleEventArgs|MockInterface|null $eventArgsMock;

    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $emMock;

    protected UnitOfWork|LegacyMockInterface|MockInterface|null $uowMock;

    protected LegacyMockInterface|SellerPackageItem|MockInterface|null $packageItemMock;

    protected ?SellerPackageItemIntegrationListener $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->busMock         = Mockery::mock(MessageBusInterface::class);
        $this->discoverMock    = Mockery::mock(IntegrationablePropertiesDiscoverService::class);
        $this->eventArgsMock   = Mockery::mock(LifecycleEventArgs::class);
        $this->emMock          = Mockery::mock(EntityManagerInterface::class);
        $this->uowMock         = Mockery::mock(UnitOfWork::class);
        $this->packageItemMock = Mockery::mock(SellerPackageItem::class);

        $this->sut = new SellerPackageItemIntegrationListener($this->busMock, $this->discoverMock);
    }

    public function testItCanDispatchMessageOnPostUpdate(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->packageItemMock), ['timcheh.seller-package-item.update'])
                           ->andReturns(["status", "actualQuantity",]);

        $this->eventArgsMock->expects('getEntityManager')->withNoArgs()->andReturns($this->emMock);

        $this->emMock->expects('getUnitOfWork')->withNoArgs()->andReturns($this->uowMock);

        $this->uowMock->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->uowMock->expects('getEntityChangeSet')
                      ->with($this->packageItemMock)
                      ->andReturns([
                          "status"    => [
                              0 => SellerPackageStatusDictionary::SENT,
                              1 => SellerPackageStatusDictionary::RECEIVED
                          ],
                          'updatedAt' => [
                              0 => new DateTimeImmutable('08:00:00'),
                              1 => new DateTimeImmutable('11:00:00'),
                          ]
                      ]);

        $this->packageItemMock->expects('getId')->withNoArgs()->andReturns(1);
        $this->packageItemMock->expects('getStatus')
                              ->withNoArgs()
                              ->andReturns(SellerPackageStatusDictionary::RECEIVED);
        $this->packageItemMock->expects('getActualQuantity')
                              ->withNoArgs()
                              ->andReturns(5);

        $this->busMock->expects('dispatch')
                      ->with(Mockery::type(UpdateSellerPackageItemMessage::class))
                      ->andReturns();

        $this->sut->onPostUpdate($this->packageItemMock, $this->eventArgsMock);
    }

    public function testItCanDoNothingOnPostUpdateWhenItHasNotIntegrationableProperties(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->packageItemMock), ['timcheh.seller-package-item.update'])
                           ->andReturns([]);

        $this->sut->onPostUpdate($this->packageItemMock, $this->eventArgsMock);
    }

    public function testItCanDoNothingOnPostUpdateWhenEntityHasNoChangeSet(): void
    {
        $this->discoverMock->expects('getIntegrationableProperties')
                           ->with(get_class($this->packageItemMock), ['timcheh.seller-package-item.update'])
                           ->andReturns(["status",]);

        $this->eventArgsMock->expects('getEntityManager')->withNoArgs()->andReturns($this->emMock);

        $this->emMock->expects('getUnitOfWork')->withNoArgs()->andReturns($this->uowMock);

        $this->uowMock->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->uowMock->expects('getEntityChangeSet')
                      ->with($this->packageItemMock)
                      ->andReturns([]);

        $this->sut->onPostUpdate($this->packageItemMock, $this->eventArgsMock);
    }
}
