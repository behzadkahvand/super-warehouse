<?php

namespace App\Tests\Unit\Listeners\Receipt;

use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\SellerPackage;
use App\Entity\SellerPackageItem;
use App\Events\Receipt\GRMarketPlacePackageCreatedEvent;
use App\Listeners\Receipt\GRMarketPlacePackageReceiptListener;
use App\Service\SellerPackage\SellerPackageStatusService;
use App\Service\SellerPackageItem\SellerPackageItemStatusService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class GRMarketPlacePackageReceiptListenerTest extends BaseUnitTestCase
{
    protected SellerPackageItem|LegacyMockInterface|MockInterface|null $packageItemMock;

    protected LegacyMockInterface|SellerPackage|MockInterface|null $packageMock;

    protected GRMarketPlacePackageReceipt|LegacyMockInterface|MockInterface|null $receiptMock;

    protected LegacyMockInterface|SellerPackageStatusService|MockInterface|null $packageStatusServiceMock;

    protected LegacyMockInterface|MockInterface|SellerPackageItemStatusService|null $packageItemStatusServiceMock;

    protected ?GRMarketPlacePackageReceiptListener $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->packageItemMock              = Mockery::mock(SellerPackageItem::class);
        $this->packageMock                  = Mockery::mock(SellerPackage::class);
        $this->receiptMock                  = Mockery::mock(GRMarketPlacePackageReceipt::class);
        $this->packageStatusServiceMock     = Mockery::mock(SellerPackageStatusService::class);
        $this->packageItemStatusServiceMock = Mockery::mock(SellerPackageItemStatusService::class);

        $this->listener = new GRMarketPlacePackageReceiptListener(
            $this->packageStatusServiceMock,
            $this->packageItemStatusServiceMock
        );
    }

    public function testItCanGetSubscribedEvents(): void
    {
        $result = $this->listener::getSubscribedEvents();

        self::assertEquals([GRMarketPlacePackageCreatedEvent::class => ['updatePackageAndPackageItemsStatus', 2]], $result);
    }

    public function testItDoNothingWhenPackageIsNotFound(): void
    {
        $this->receiptMock->shouldReceive('getReference')
                          ->once()
                          ->withNoArgs()
                          ->andReturnNull();

        $this->packageStatusServiceMock->shouldNotReceive('updatePackageStatus');

        $this->packageItemStatusServiceMock->shouldNotReceive('updatePackageItemStatus');

        $event = new GRMarketPlacePackageCreatedEvent($this->receiptMock);

        $this->listener->updatePackageAndPackageItemsStatus($event);
    }

    public function testItDoNothingWhenPackageHasNoItems(): void
    {
        $this->packageMock->shouldReceive('getPackageItems')
                          ->once()
                          ->withNoArgs()
                          ->andReturn(new ArrayCollection([]));

        $this->receiptMock->shouldReceive('getReference')
                          ->once()
                          ->withNoArgs()
                          ->andReturn($this->packageMock);

        $this->packageStatusServiceMock->shouldNotReceive('updatePackageStatus');

        $this->packageItemStatusServiceMock->shouldNotReceive('updatePackageItemStatus');

        $event = new GRMarketPlacePackageCreatedEvent($this->receiptMock);

        $this->listener->updatePackageAndPackageItemsStatus($event);
    }

    public function testItCanUpdatePackageAndPackageItemsStatus(): void
    {
        $this->packageMock->shouldReceive('getPackageItems')
                          ->once()
                          ->withNoArgs()
                          ->andReturn(new ArrayCollection([$this->packageItemMock]));

        $this->receiptMock->shouldReceive('getReference')
                          ->once()
                          ->withNoArgs()
                          ->andReturn($this->packageMock);

        $event = new GRMarketPlacePackageCreatedEvent($this->receiptMock);

        $this->packageStatusServiceMock->shouldReceive('updatePackageStatus')
                                       ->once()
                                       ->with($this->packageMock)
                                       ->andReturn($this->packageMock);


        $this->packageItemStatusServiceMock->shouldReceive('updatePackageItemStatus')
                                           ->once()
                                           ->with($this->packageItemMock)
                                           ->andReturn($this->packageItemMock);

        $this->listener->updatePackageAndPackageItemsStatus($event);
    }
}
