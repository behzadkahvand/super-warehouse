<?php

namespace App\Service\ReceiptItem;

use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\ReceiptItemData;
use App\Entity\ReceiptItem;
use App\Events\ReceiptItem\StoringReceiptItemManuallyEvent;
use App\Service\Receipt\ReceiptItemFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ReceiptItemManualService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher,
        private ReceiptItemFactory $receiptItemFactory
    ) {
    }

    public function create(ReceiptItemData $receiptItemData): ReceiptItem
    {
        $receiptItem = $this->receiptItemFactory->create();

        $receiptItem->setStatus(ReceiptStatusDictionary::DRAFT)
                    ->setInventory($receiptItemData->getInventory())
                    ->setReceipt($receiptItemData->getReceipt())
                    ->setQuantity($receiptItemData->getQuantity());

        $this->dispatcher->dispatch(new StoringReceiptItemManuallyEvent($receiptItem));

        $this->entityManager->persist($receiptItem);
        $this->entityManager->flush();

        return $receiptItem;
    }

    public function update(ReceiptItemData $receiptItemData, ReceiptItem $receiptItem): ReceiptItem
    {
        $receiptItem->setQuantity($receiptItemData->getQuantity());

        $this->dispatcher->dispatch(new StoringReceiptItemManuallyEvent($receiptItem));

        $this->entityManager->flush();

        return $receiptItem;
    }
}
