<?php

namespace App\Listeners\Receipt;

use App\Entity\SellerPackage;
use App\Events\Receipt\GRMarketPlacePackageCreatedEvent;
use App\Service\SellerPackage\SellerPackageStatusService;
use App\Service\SellerPackageItem\SellerPackageItemStatusService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GRMarketPlacePackageReceiptListener implements EventSubscriberInterface
{
    public function __construct(
        private SellerPackageStatusService $packageStatusService,
        private SellerPackageItemStatusService $packageItemStatusService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GRMarketPlacePackageCreatedEvent::class => ['updatePackageAndPackageItemsStatus', 2],
        ];
    }

    public function updatePackageAndPackageItemsStatus(GRMarketPlacePackageCreatedEvent $event): void
    {
        /** @var SellerPackage $sellerPackage */
        $sellerPackage = $event->getReceipt()->getReference();

        if (!$sellerPackage) {
            return;
        }

        $packageItems = $sellerPackage->getPackageItems();

        if ($packageItems->isEmpty()) {
            return;
        }

        foreach ($packageItems as $packageItem) {
            $this->packageItemStatusService->updatePackageItemStatus($packageItem);
        }

        $this->packageStatusService->updatePackageStatus($sellerPackage);
    }
}
