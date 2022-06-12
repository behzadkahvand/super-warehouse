<?php

namespace App\Service\StatusTransition\AllowTransitions\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\AllowTransitionConfigData;
use App\Service\StatusTransition\AllowTransitions\StateAllowedTransitionInterface;

class STOutboundReceiptAllowedTransition implements StateAllowedTransitionInterface
{
    public function __invoke(): AllowTransitionConfigData
    {
        return (new AllowTransitionConfigData())
            ->setDefault(ReceiptStatusDictionary::DRAFT)
            ->addAllowTransitions(ReceiptStatusDictionary::DRAFT, [ReceiptStatusDictionary::APPROVED])
            ->addAllowTransitions(ReceiptStatusDictionary::APPROVED, [ReceiptStatusDictionary::READY_TO_PICK])
            ->addAllowTransitions(ReceiptStatusDictionary::READY_TO_PICK, [ReceiptStatusDictionary::PICKING])
            ->addAllowTransitions(ReceiptStatusDictionary::PICKING, [ReceiptStatusDictionary::DONE]);
    }
}
