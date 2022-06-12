<?php

namespace App\Tests\Unit\Service\SellerPackage;

use App\Entity\SellerPackage;
use App\Service\SellerPackage\SellerPackageStatus\SellerPackageCanceledStatus;
use App\Service\SellerPackage\SellerPackageStatus\SellerPackagePartialReceivedStatus;
use App\Service\SellerPackage\SellerPackageStatus\SellerPackageReceivedStatus;
use App\Service\SellerPackage\SellerPackageStatusService;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class SellerPackageStatusServiceTest extends MockeryTestCase
{
    public function testUpdatePackageStatus(): void
    {
        $manager = Mockery::mock(EntityManagerInterface::class);
        $manager->shouldReceive('flush')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $sellerPackage = Mockery::mock(SellerPackage::class);

        $receivedStatus = Mockery::mock(SellerPackageReceivedStatus::class);
        $receivedStatus->shouldReceive('supports')
                       ->once()
                       ->with($sellerPackage)
                       ->andReturnFalse();

        $partialReceived = Mockery::mock(SellerPackagePartialReceivedStatus::class);
        $partialReceived->shouldReceive('supports')
                        ->once()
                        ->with($sellerPackage)
                        ->andReturnTrue();
        $partialReceived->shouldReceive('setStatus')
                        ->once()
                        ->with($sellerPackage)
                        ->andReturn($sellerPackage);

        $notReceived = Mockery::mock(SellerPackageCanceledStatus::class);
        $notReceived->shouldReceive('supports')
                    ->once()
                    ->with($sellerPackage)
                    ->andReturnFalse();

        $sellerPackageStatusService = new SellerPackageStatusService(
            $manager,
            [$receivedStatus, $notReceived, $partialReceived]
        );
        $sellerPackage              = $sellerPackageStatusService->updatePackageStatus($sellerPackage);

        self::assertInstanceOf(SellerPackage::class, $sellerPackage);
    }
}
