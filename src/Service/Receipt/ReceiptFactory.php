<?php

namespace App\Service\Receipt;

use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Entity\Receipt;
use App\Entity\Receipt\GINoneReceipt;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\Receipt\GRNoneReceipt;
use App\Entity\Receipt\STInboundReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use App\Service\Receipt\Exceptions\ReferenceTypeNotFoundException;

final class ReceiptFactory
{
    public function create(string $referenceType): Receipt
    {
        return match ($referenceType) {
            ReceiptReferenceTypeDictionary::GI_NONE => new GINoneReceipt(),
            ReceiptReferenceTypeDictionary::GI_SHIPMENT => new GIShipmentReceipt(),
            ReceiptReferenceTypeDictionary::GR_MP_PACKAGE => new GRMarketPlacePackageReceipt(),
            ReceiptReferenceTypeDictionary::GR_NONE => new GRNoneReceipt(),
            ReceiptReferenceTypeDictionary::ST_INBOUND => new STInboundReceipt(),
            ReceiptReferenceTypeDictionary::ST_OUTBOUND => new STOutboundReceipt(),
            default => throw new ReferenceTypeNotFoundException()
        };
    }
}
