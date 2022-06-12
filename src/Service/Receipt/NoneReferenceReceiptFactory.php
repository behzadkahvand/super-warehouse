<?php

namespace App\Service\Receipt;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\Receipt\GINoneReceipt;
use App\Entity\Receipt\GRNoneReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use App\Service\Receipt\Exceptions\ReceiptTypeNotFoundException;

class NoneReferenceReceiptFactory
{
    public function create(string $receiptType): Receipt
    {
        return match ($receiptType) {
            ReceiptTypeDictionary::GOOD_ISSUE => new GINoneReceipt(),
            ReceiptTypeDictionary::GOOD_RECEIPT => new GRNoneReceipt(),
            ReceiptTypeDictionary::STOCK_TRANSFER => new STOutboundReceipt(),
            default => throw new ReceiptTypeNotFoundException()
        };
    }
}
