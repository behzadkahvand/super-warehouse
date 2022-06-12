<?php

namespace App\Service\Receipt;

use App\Entity\ReceiptItem;

final class ReceiptItemFactory
{
    public function create(): ReceiptItem
    {
        return new ReceiptItem();
    }
}
