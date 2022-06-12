<?php

namespace App\Listeners\ReceiptItem;

use App\Dictionary\ReceiptTypeDictionary;
use App\Events\ReceiptItem\StoringReceiptItemManuallyEvent;
use App\Exceptions\WarehouseStock\LackOfPhysicalStockException;
use App\Service\WarehouseStock\CheckWarehouseStockWithReceiptItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckPhysicalStockReceiptItemListener implements EventSubscriberInterface
{
    public function __construct(private CheckWarehouseStockWithReceiptItem $checkWarehouseStockWithReceiptItem)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StoringReceiptItemManuallyEvent::class => ['checkWarehousePhysicalStock', 1],
        ];
    }

    public function checkWarehousePhysicalStock(StoringReceiptItemManuallyEvent $event): void
    {
        $receiptItem = $event->getReceiptItem();
        $receipt     = $receiptItem->getReceipt();

        if (ReceiptTypeDictionary::STOCK_TRANSFER !== $receipt->getType()) {
            return;
        }

        if (!$this->checkWarehouseStockWithReceiptItem->physicalStock($receiptItem)) {
            throw new LackOfPhysicalStockException();
        }
    }
}
