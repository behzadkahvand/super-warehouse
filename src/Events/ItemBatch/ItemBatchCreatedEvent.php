<?php

namespace App\Events\ItemBatch;

use App\Entity\ItemBatch;
use App\Entity\ReceiptItem;
use Symfony\Contracts\EventDispatcher\Event;

final class ItemBatchCreatedEvent extends Event
{
    public function __construct(private ItemBatch $itemBatch, private ReceiptItem $receiptItem)
    {
    }

    public function getItemBatch(): ItemBatch
    {
        return $this->itemBatch;
    }

    public function getReceiptItem(): ReceiptItem
    {
        return $this->receiptItem;
    }
}
