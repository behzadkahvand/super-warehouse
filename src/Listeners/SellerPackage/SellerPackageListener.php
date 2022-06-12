<?php

namespace App\Listeners\SellerPackage;

use App\Events\SellerPackage\SellerPackageCanceledEvent;
use App\Messaging\Messages\Command\SellerPackage\SellerPackageCanceledMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SellerPackageListener implements EventSubscriberInterface
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SellerPackageCanceledEvent::class => 'updateShopSellerPackage',
        ];
    }

    public function updateShopSellerPackage(SellerPackageCanceledEvent $event): void
    {
        $sellerPackage        = $event->getSellerPackage();
        $sellerPackageMessage = (new SellerPackageCanceledMessage())
            ->setSellerPackageId($sellerPackage->getId());

        //@TODO handler for seller package canceled!
        $this->bus->dispatch(async_message($sellerPackageMessage));
    }
}
