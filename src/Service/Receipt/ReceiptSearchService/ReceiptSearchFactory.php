<?php

namespace App\Service\Receipt\ReceiptSearchService;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\Receipt\STInboundReceipt;
use App\Service\Receipt\Exceptions\ReceiptTypeNotFoundException;

class ReceiptSearchFactory
{
    public function getResourceReceiptClass(bool $referenceIsFiltered, ?string $receiptType): string
    {
        if (!$referenceIsFiltered) {
            return Receipt::class;
        }

        return match ($receiptType) {
            ReceiptTypeDictionary::GOOD_RECEIPT => GRMarketPlacePackageReceipt::class,
            ReceiptTypeDictionary::GOOD_ISSUE => GIShipmentReceipt::class,
            ReceiptTypeDictionary::STOCK_TRANSFER => STInboundReceipt::class,
            default => throw new ReceiptTypeNotFoundException(),
        };
    }
}
