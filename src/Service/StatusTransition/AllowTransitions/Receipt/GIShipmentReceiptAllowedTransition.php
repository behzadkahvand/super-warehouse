<?php

namespace App\Service\StatusTransition\AllowTransitions\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\AllowTransitionConfigData;
use App\Service\StatusTransition\AllowTransitions\StateAllowedTransitionInterface;

class GIShipmentReceiptAllowedTransition implements StateAllowedTransitionInterface
{
    public function __invoke(): AllowTransitionConfigData
    {
        return (new AllowTransitionConfigData())
            ->setDefault(ReceiptStatusDictionary::RESERVED)
            ->addAllowTransitions(
                ReceiptStatusDictionary::RESERVED,
                [
                    ReceiptStatusDictionary::WAITING_FOR_SUPPLY,
                    ReceiptStatusDictionary::APPROVED,
                    ReceiptStatusDictionary::CANCELED,
                ]
            )
            ->addAllowTransitions(
                ReceiptStatusDictionary::WAITING_FOR_SUPPLY,
                [ReceiptStatusDictionary::APPROVED, ReceiptStatusDictionary::CANCELED]
            )
            ->addAllowTransitions(
                ReceiptStatusDictionary::APPROVED,
                [ReceiptStatusDictionary::READY_TO_PICK, ReceiptStatusDictionary::CANCELED]
            )
            ->addAllowTransitions(ReceiptStatusDictionary::READY_TO_PICK, [ReceiptStatusDictionary::PICKING])
            ->addAllowTransitions(ReceiptStatusDictionary::PICKING, [ReceiptStatusDictionary::DONE]);
    }
}
