<?php

namespace App\Service\StatusTransition\AllowTransitions\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\AllowTransitionConfigData;
use App\Service\StatusTransition\AllowTransitions\StateAllowedTransitionInterface;

class STInboundReceiptAllowedTransition implements StateAllowedTransitionInterface
{
    public function __invoke(): AllowTransitionConfigData
    {
        return (new AllowTransitionConfigData())
            ->setDefault(ReceiptStatusDictionary::APPROVED)
            ->addAllowTransitions(ReceiptStatusDictionary::APPROVED, [ReceiptStatusDictionary::READY_TO_STOW])
            ->addAllowTransitions(ReceiptStatusDictionary::READY_TO_STOW, [ReceiptStatusDictionary::STOWING])
            ->addAllowTransitions(ReceiptStatusDictionary::STOWING, [ReceiptStatusDictionary::DONE]);
    }
}
