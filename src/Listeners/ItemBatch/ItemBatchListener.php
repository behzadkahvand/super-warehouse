<?php

namespace App\Listeners\ItemBatch;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Events\ItemBatch\ItemBatchCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ItemBatchListener implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $manager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ItemBatchCreatedEvent::class => 'updateReceiptAndReceiptItemStatus',
        ];
    }

    public function updateReceiptAndReceiptItemStatus(ItemBatchCreatedEvent $event): void
    {
        $receiptItem = $event->getReceiptItem();
        $receipt     = $receiptItem->getReceipt();

        $receiptItem->setStatus(ReceiptStatusDictionary::BATCH_PROCESSING);

        if ($this->checkAllItemsAreInBatchProcessing($receipt)) {
            $receipt->setStatus(ReceiptStatusDictionary::BATCH_PROCESSING);
        }

        $this->manager->flush();
    }

    private function checkAllItemsAreInBatchProcessing(Receipt $receipt): bool
    {
        return collect($receipt->getReceiptItems())->every(fn(ReceiptItem $receiptItem
        ) => $receiptItem->getStatus() === ReceiptStatusDictionary::BATCH_PROCESSING);
    }
}
