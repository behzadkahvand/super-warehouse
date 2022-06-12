<?php

namespace App\Dictionary;

class ShipmentGIReceiptMappingStatusDictionary extends Dictionary
{
    public const MAPPED_STATUS = [
        ReceiptStatusDictionary::WAITING_FOR_SUPPLY => ShipmentStatusDictionary::WAITING_FOR_SUPPLY,
        ReceiptStatusDictionary::APPROVED           => ShipmentStatusDictionary::WAREHOUSE,
        ReceiptStatusDictionary::READY_TO_PICK      => ShipmentStatusDictionary::PREPARING,
        ReceiptStatusDictionary::PICKING            => ShipmentStatusDictionary::PREPARING,
        ReceiptStatusDictionary::DONE               => ShipmentStatusDictionary::PREPARED,
        ReceiptStatusDictionary::CANCELED           => ShipmentStatusDictionary::CANCELED,
    ];
}
