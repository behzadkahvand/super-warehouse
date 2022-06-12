<?php

namespace App\Service\PullList\ConfirmedPullListByLocator;

use App\Dictionary\PullListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Admin;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Repository\PullListRepository;
use App\Service\PullList\ConfirmedPullListByLocator\Exceptions\PullListNotFoundException;
use App\Service\StatusTransition\StateTransitionHandlerService;

class ConfirmedPullListByLocatorService
{
    public function __construct(
        private PullListRepository $pullListRepository,
        private StateTransitionHandlerService $stateTransitionHandler
    ) {
    }

    public function perform(int $pullListId, Admin $locator): PullList
    {
        $pullList = $this->pullListRepository->findOneBy([
            'id'      => $pullListId,
            'locator' => $locator,
            'status'  => PullListStatusDictionary::SENT_TO_LOCATOR
        ]);

        if (!$pullList) {
            throw new PullListNotFoundException();
        }

        $pullListItems = $pullList->getItems();

        $this->stateTransitionHandler->batchTransitState(
            $pullListItems->toArray(),
            PullListStatusDictionary::CONFIRMED_BY_LOCATOR,
            $locator
        );

        $receiptItems = $pullListItems->map(fn(PullListItem $pullListItem) => $pullListItem->getReceiptItem());

        $this->stateTransitionHandler->batchTransitState(
            $receiptItems->toArray(),
            ReceiptStatusDictionary::STOWING,
            $locator
        );

        return $pullList;
    }
}
