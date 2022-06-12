<?php

namespace App\Service\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\DTO\GRMarketPlacePackageReceiptData;
use App\Entity\Receipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\SellerPackageItem;
use App\Events\Receipt\GRMarketPlacePackageCreatedEvent;
use App\Service\Receipt\Exceptions\ReceiptHasNoItemException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GRMarketPlacePackageReceiptService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ReceiptItemFactory $receiptItemFactory,
        private EventDispatcherInterface $dispatcher,
        private ReceiptFactory $receiptFactory
    ) {
    }

    public function makeReceipt(GRMarketPlacePackageReceiptData $receiptData): Receipt
    {
        /** @var GRMarketPlacePackageReceipt $receipt */
        $receipt = $this->receiptFactory->create(ReceiptReferenceTypeDictionary::GR_MP_PACKAGE);
        $receipt->setStatus(ReceiptStatusDictionary::APPROVED);
        $receipt->setSourceWarehouse($receiptData->getWarehouse());
        $receipt->setReference($receiptData->getSellerPackage());
        $this->manager->persist($receipt);

        $this->createAndFillReceiptItems($receipt);

        $this->manager->flush();

        $this->dispatcher->dispatch(new GRMarketPlacePackageCreatedEvent($receipt));

        return $receipt;
    }

    public function createAndFillReceiptItems(Receipt $receipt): void
    {
        $packageSeller = $receipt->getReference();

        $hasItem = false;
        /** @var SellerPackageItem $packageItem */
        foreach ($packageSeller->getPackageItems() as $packageItem) {
            if ($packageItem->hasActual()) {
                $hasItem = true;
                $receiptItem = $this->receiptItemFactory->create();
                $receiptItem->setReceipt($receipt);
                $receiptItem->setStatus(ReceiptStatusDictionary::APPROVED);
                $receiptItem->setQuantity($packageItem->getActualQuantity());
                $receiptItem->setInventory($packageItem->getInventory());
                $this->manager->persist($receiptItem);
            }
        }

        if (!$hasItem) {
            throw new ReceiptHasNoItemException();
        }
    }
}
