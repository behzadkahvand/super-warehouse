<?php

namespace App\Events\ReceiptItem;

use App\Entity\ReceiptItem;

final class StoringReceiptItemManuallyEvent
{
    public function __construct(private ReceiptItem $receiptItem)
    {
    }

    /**
     * @return ReceiptItem
     */
    public function getReceiptItem(): ReceiptItem
    {
        return $this->receiptItem;
    }
}
