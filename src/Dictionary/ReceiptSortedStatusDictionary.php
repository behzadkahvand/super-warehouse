<?php

namespace App\Dictionary;

class ReceiptSortedStatusDictionary extends Dictionary
{
    public const GINONERECEIPT = [
        ReceiptStatusDictionary::DRAFT,
        ReceiptStatusDictionary::APPROVED,
        ReceiptStatusDictionary::READY_TO_PICK,
        ReceiptStatusDictionary::PICKING,
        ReceiptStatusDictionary::DONE
    ];

    public const GISHIPMENTRECEIPT = [
        ReceiptStatusDictionary::RESERVED,
        ReceiptStatusDictionary::WAITING_FOR_SUPPLY,
        ReceiptStatusDictionary::APPROVED,
        ReceiptStatusDictionary::READY_TO_PICK,
        ReceiptStatusDictionary::PICKING,
        ReceiptStatusDictionary::DONE,
        ReceiptStatusDictionary::CANCELED,
    ];

    public const GRMARKETPLACEPACKAGERECEIPT = [
        ReceiptStatusDictionary::APPROVED,
        ReceiptStatusDictionary::BATCH_PROCESSING,
        ReceiptStatusDictionary::LABEL_PRINTING,
        ReceiptStatusDictionary::READY_TO_STOW,
        ReceiptStatusDictionary::STOWING,
        ReceiptStatusDictionary::DONE,
        ReceiptStatusDictionary::CANCELED,
    ];

    public const GRNONERECEIPT = [
        ReceiptStatusDictionary::DRAFT,
        ReceiptStatusDictionary::APPROVED,
        ReceiptStatusDictionary::BATCH_PROCESSING,
        ReceiptStatusDictionary::LABEL_PRINTING,
        ReceiptStatusDictionary::READY_TO_STOW,
        ReceiptStatusDictionary::STOWING,
        ReceiptStatusDictionary::DONE,
        ReceiptStatusDictionary::CANCELED,
    ];

    public const STINBOUNDRECEIPT = [
        ReceiptStatusDictionary::APPROVED,
        ReceiptStatusDictionary::READY_TO_STOW,
        ReceiptStatusDictionary::STOWING,
        ReceiptStatusDictionary::DONE,
        ReceiptStatusDictionary::CANCELED,
    ];

    public const STOUTBOUNDRECEIPT = [
        ReceiptStatusDictionary::DRAFT,
        ReceiptStatusDictionary::APPROVED,
        ReceiptStatusDictionary::READY_TO_PICK,
        ReceiptStatusDictionary::PICKING,
        ReceiptStatusDictionary::DONE,
    ];
}
