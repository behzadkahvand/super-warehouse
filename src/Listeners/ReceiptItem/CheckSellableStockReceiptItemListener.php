<?php

namespace App\Listeners\ReceiptItem;

use App\Dictionary\ReceiptTypeDictionary;
use App\Events\ReceiptItem\StoringReceiptItemManuallyEvent;
use App\Exceptions\WarehouseStock\LackOfSellableStockException;
use App\Service\WarehouseStock\CheckWarehouseStockWithReceiptItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckSellableStockReceiptItemListener implements EventSubscriberInterface
{
    public function __construct(private CheckWarehouseStockWithReceiptItem $checkWarehouseStockWithReceiptItem)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StoringReceiptItemManuallyEvent::class => ['checkWarehouseSellableStock', 1],
        ];
    }

    public function checkWarehouseSellableStock(StoringReceiptItemManuallyEvent $event): void
    {
        $receiptItem = $event->getReceiptItem();
        $receipt     = $receiptItem->getReceipt();

        if (ReceiptTypeDictionary::GOOD_ISSUE !== $receipt->getType()) {
            return;
        }

        if (!$this->checkWarehouseStockWithReceiptItem->sellableStock($receiptItem)) {
            throw new LackOfSellableStockException();
        }
    }
}
