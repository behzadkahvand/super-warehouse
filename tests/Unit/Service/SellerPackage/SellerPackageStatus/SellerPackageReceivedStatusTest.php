<?php

namespace App\Tests\Unit\Service\SellerPackage\SellerPackageStatus;

use App\Entity\SellerPackage;
use App\Entity\SellerPackageItem;
use App\Service\SellerPackage\SellerPackageStatus\SellerPackageReceivedStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SellerPackageReceivedStatusTest extends MockeryTestCase
{
    public function testSupports(): void
    {
        $packageItem1 = Mockery::mock(SellerPackageItem::class);
        $packageItem1->shouldReceive('isReceived')
                     ->once()
                     ->withNoArgs()
                     ->andReturnTrue();

        $packageItem2 = Mockery::mock(SellerPackageItem::class);
        $packageItem2->shouldReceive('isReceived')
                     ->once()
                     ->withNoArgs()
                     ->andReturnTrue();
        $sellerPackage = Mockery::mock(SellerPackage::class);
        $sellerPackage->shouldReceive('getPackageItems')
                      ->once()
                      ->withNoArgs()
                      ->andReturn(new ArrayCollection([$packageItem1, $packageItem2]));

        $sellerPackageReceivedStatus = new SellerPackageReceivedStatus();
        self::assertTrue($sellerPackageReceivedStatus->supports($sellerPackage));
    }

    public function testDoesNotSupports(): void
    {
        $packageItem1 = Mockery::mock(SellerPackageItem::class);
        $packageItem1->shouldReceive('isReceived')
                     ->once()
                     ->withNoArgs()
                     ->andReturnTrue();

        $packageItem2 = Mockery::mock(SellerPackageItem::class);
        $packageItem2->shouldReceive('isReceived')
                     ->once()
                     ->withNoArgs()
                     ->andReturnFalse();
        $sellerPackage = Mockery::mock(SellerPackage::class);
        $sellerPackage->shouldReceive('getPackageItems')
                      ->once()
                      ->withNoArgs()
                      ->andReturn(new ArrayCollection([$packageItem1, $packageItem2]));

        $sellerPackageReceivedStatus = new SellerPackageReceivedStatus();
        self::assertFalse($sellerPackageReceivedStatus->supports($sellerPackage));
    }

    public function testSetStatus(): void
    {
        $sellerPackage = Mockery::mock(SellerPackage::class);
        $sellerPackage->shouldReceive('setStatus')
                      ->once()
                      ->with(Mockery::type('string'))
                      ->andReturnSelf();

        $sellerPackageReceivedStatus = new SellerPackageReceivedStatus();
        self::assertInstanceOf(SellerPackage::class, $sellerPackageReceivedStatus->setStatus($sellerPackage));
    }
}
