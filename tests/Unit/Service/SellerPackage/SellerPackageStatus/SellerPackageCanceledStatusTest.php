<?php

namespace App\Tests\Unit\Service\SellerPackage\SellerPackageStatus;

use App\Entity\SellerPackage;
use App\Entity\SellerPackageItem;
use App\Service\SellerPackage\SellerPackageStatus\SellerPackageCanceledStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SellerPackageCanceledStatusTest extends MockeryTestCase
{
    public function testSupports(): void
    {
        $packageItem1 = Mockery::mock(SellerPackageItem::class);
        $packageItem1->shouldReceive('isCanceled')
                     ->once()
                     ->withNoArgs()
                     ->andReturnTrue();

        $packageItem2 = Mockery::mock(SellerPackageItem::class);
        $packageItem2->shouldReceive('isCanceled')
                     ->once()
                     ->withNoArgs()
                     ->andReturnTrue();
        $sellerPackage = Mockery::mock(SellerPackage::class);
        $sellerPackage->shouldReceive('getPackageItems')
                      ->once()
                      ->withNoArgs()
                      ->andReturn(new ArrayCollection([$packageItem1, $packageItem2]));

        $sellerPackageNotReceivedStatus = new SellerPackageCanceledStatus();
        self::assertTrue($sellerPackageNotReceivedStatus->supports($sellerPackage));
    }

    public function testDoesNotSupports(): void
    {
        $packageItem1 = Mockery::mock(SellerPackageItem::class);
        $packageItem1->shouldReceive('isCanceled')
                     ->once()
                     ->withNoArgs()
                     ->andReturnTrue();

        $packageItem2 = Mockery::mock(SellerPackageItem::class);
        $packageItem2->shouldReceive('isCanceled')
                     ->once()
                     ->withNoArgs()
                     ->andReturnFalse();
        $sellerPackage = Mockery::mock(SellerPackage::class);
        $sellerPackage->shouldReceive('getPackageItems')
                      ->once()
                      ->withNoArgs()
                      ->andReturn(new ArrayCollection([$packageItem1, $packageItem2]));

        $sellerPackageNotReceivedStatus = new SellerPackageCanceledStatus();
        self::assertFalse($sellerPackageNotReceivedStatus->supports($sellerPackage));
    }

    public function testSetStatus(): void
    {
        $sellerPackage = Mockery::mock(SellerPackage::class);
        $sellerPackage->shouldReceive('setStatus')
                      ->once()
                      ->with(Mockery::type('string'))
                      ->andReturnSelf();

        $sellerPackageNotReceivedStatus = new SellerPackageCanceledStatus();
        self::assertInstanceOf(SellerPackage::class, $sellerPackageNotReceivedStatus->setStatus($sellerPackage));
    }
}
