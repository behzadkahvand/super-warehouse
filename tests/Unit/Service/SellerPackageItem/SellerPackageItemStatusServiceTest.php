<?php

namespace App\Tests\Unit\Service\SellerPackageItem;

use App\Entity\SellerPackageItem;
use App\Service\SellerPackageItem\SellerPackageItemStatus\SellerPackageItemCanceledStatus;
use App\Service\SellerPackageItem\SellerPackageItemStatus\SellerPackageItemPartialReceivedStatus;
use App\Service\SellerPackageItem\SellerPackageItemStatus\SellerPackageItemReceivedStatus;
use App\Service\SellerPackageItem\SellerPackageItemStatusService;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SellerPackageItemStatusServiceTest extends MockeryTestCase
{
    public function testUpdatePackageItemStatus(): void
    {
        $manager = Mockery::mock(EntityManagerInterface::class);
        $manager->shouldReceive('flush')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $sellerPackageItem = Mockery::mock(SellerPackageItem::class);

        $receivedStatus = Mockery::mock(SellerPackageItemReceivedStatus::class);
        $receivedStatus->shouldReceive('supports')
                       ->once()
                       ->with($sellerPackageItem)
                       ->andReturnFalse();

        $partialReceived = Mockery::mock(SellerPackageItemPartialReceivedStatus::class);
        $partialReceived->shouldReceive('supports')
                        ->once()
                        ->with($sellerPackageItem)
                        ->andReturnTrue();
        $partialReceived->shouldReceive('setStatus')
                        ->once()
                        ->with($sellerPackageItem)
                        ->andReturn($sellerPackageItem);

        $canceled = Mockery::mock(SellerPackageItemCanceledStatus::class);
        $canceled->shouldReceive('supports')
                 ->once()
                 ->with($sellerPackageItem)
                 ->andReturnFalse();

        $sellerPackageStatusService = new SellerPackageItemStatusService(
            $manager,
            [$receivedStatus, $canceled, $partialReceived]
        );
        $sellerPackageItem          = $sellerPackageStatusService->updatePackageItemStatus($sellerPackageItem);

        self::assertInstanceOf(SellerPackageItem::class, $sellerPackageItem);
    }
}
