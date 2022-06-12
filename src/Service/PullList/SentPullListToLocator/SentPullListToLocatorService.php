<?php

namespace App\Service\PullList\SentPullListToLocator;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\PullList;
use App\Service\PullList\SentPullListToLocator\Exceptions\PullListHasNoItemException;
use App\Service\StatusTransition\StateTransitionHandlerService;

class SentPullListToLocatorService
{
    public function __construct(private StateTransitionHandlerService $stateTransitionHandler)
    {
    }

    public function perform(PullList $pullList): void
    {
        if (!$pullList->hasItems()) {
            throw new PullListHasNoItemException();
        }

        $this->stateTransitionHandler->batchTransitState(
            $pullList->getItems()->toArray(),
            PullListStatusDictionary::SENT_TO_LOCATOR
        );
    }
}
