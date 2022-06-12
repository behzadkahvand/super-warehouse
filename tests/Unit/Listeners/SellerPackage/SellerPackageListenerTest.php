<?php

namespace App\Tests\Unit\Listeners\SellerPackage;

use App\Entity\SellerPackage;
use App\Events\SellerPackage\SellerPackageCanceledEvent;
use App\Listeners\SellerPackage\SellerPackageListener;
use App\Messaging\Messages\Command\Async\AsyncMessage;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class SellerPackageListenerTest extends MockeryTestCase
{
    public function testUpdateShopSellerPackage(): void
    {
        $sellerPackage = Mockery::mock(SellerPackage::class);
        $bus           = Mockery::mock(MessageBusInterface::class);

        $sellerPackage->shouldReceive('getId')
                      ->once()
                      ->withNoArgs()
                      ->andReturn(1);

        $bus->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(AsyncMessage::class))
            ->andReturn(new Envelope(new stdClass()));

        $event = new SellerPackageCanceledEvent($sellerPackage);

        $listener = new SellerPackageListener($bus);
        $listener->updateShopSellerPackage($event);
    }
}
