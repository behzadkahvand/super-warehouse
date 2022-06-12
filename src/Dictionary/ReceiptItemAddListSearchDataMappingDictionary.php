<?php

namespace App\Dictionary;

class ReceiptItemAddListSearchDataMappingDictionary extends Dictionary
{
    public const FILTERS = [
        'receiptItemId' => 'id',
        'receiptId'     => 'receipt.id',
        'productId'     => 'inventory.product.id',
        'InventoryId'   => 'inventory.id',
    ];

    public const SORTS = [];
}
