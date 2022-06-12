<?php

namespace App\Listeners\ItemSerial;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ReceiptItem;
use App\Events\ItemSerial\ItemBatchSerialsCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ItemSerialListener implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ItemBatchSerialsCreatedEvent::class => 'updateReceiptItemStatus',
        ];
    }

    public function updateReceiptItemStatus(ItemBatchSerialsCreatedEvent $event): void
    {
        $itemBatch = $event->getItemBatch();

        /** @var ReceiptItem $receiptItem */
        $receiptItem = $itemBatch->getReceiptItemBatches()->first()->getReceiptItem();

        $receiptItemSerialsCount = $receiptItem->getReceiptItemSerials()->count();
        if ($receiptItemSerialsCount == $receiptItem->getQuantity()) {
            $receiptItem->setStatus(ReceiptStatusDictionary::READY_TO_STOW);
        } elseif ($receiptItemSerialsCount > 0) {
            $receiptItem->setStatus(ReceiptStatusDictionary::LABEL_PRINTING);
        }

        $this->manager->flush();
    }
}
