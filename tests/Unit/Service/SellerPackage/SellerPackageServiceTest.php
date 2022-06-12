<?php

namespace App\Tests\Unit\Service\SellerPackage;

use App\Dictionary\SellerPackageStatusDictionary;
use App\Entity\SellerPackage;
use App\Entity\SellerPackageItem;
use App\Events\SellerPackage\SellerPackageCanceledEvent;
use App\Service\SellerPackage\SellerPackageService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SellerPackageServiceTest extends MockeryTestCase
{
    public function testCancel(): void
    {
        $manager = Mockery::mock(EntityManagerInterface::class);
        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $sellerPackage = Mockery::mock(SellerPackage::class);

        $sellerPackage->shouldReceive('getStatus')
            ->once()
            ->withNoArgs()
            ->andReturn(SellerPackageStatusDictionary::SENT);

        $sellerPackage->shouldReceive('setStatus')
                      ->once()
                      ->with(Mockery::type('string'))
                      ->andReturnSelf();

        $sellerPackage->shouldReceive('getPackageItems')
            ->once()
            ->withNoArgs()
            ->andReturn(new ArrayCollection([new SellerPackageItem()]));

        $manager->shouldReceive('flush')
            ->once()
            ->withNoArgs()
            ->andReturn();

        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(SellerPackageCanceledEvent::class))
            ->andReturn(new stdClass());

        $sellerPackageService = new SellerPackageService($manager, $dispatcher);
        $sellerPackageService->cancel($sellerPackage);
    }
}
