<?php

namespace App\Service\StatusTransition\AllowTransitions\PullList;

use App\Dictionary\PullListStatusDictionary;
use App\DTO\AllowTransitionConfigData;
use App\Service\StatusTransition\AllowTransitions\StateAllowedTransitionInterface;

class PullListAllowedTransition implements StateAllowedTransitionInterface
{
    public function __invoke(): AllowTransitionConfigData
    {
        return (new AllowTransitionConfigData())
            ->setDefault(PullListStatusDictionary::DRAFT)
            ->addAllowTransition(PullListStatusDictionary::DRAFT, PullListStatusDictionary::SENT_TO_LOCATOR)
            ->addAllowTransition(
                PullListStatusDictionary::SENT_TO_LOCATOR,
                PullListStatusDictionary::CONFIRMED_BY_LOCATOR
            )
            ->addAllowTransition(PullListStatusDictionary::CONFIRMED_BY_LOCATOR, PullListStatusDictionary::STOWING)
            ->addAllowTransition(PullListStatusDictionary::STOWING, PullListStatusDictionary::CLOSED);
    }
}
