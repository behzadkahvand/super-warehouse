<?php

namespace App\Service\StatusTransition\Subscribers\PullList;

use App\Dictionary\PullListSortedStatusDictionary;
use App\Dictionary\PullListStatusDictionary;
use App\DTO\StateSubscriberData;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Service\StatusTransition\ParentItemStateService;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Service\StatusTransition\Subscribers\StateSubscriberInterface;

class PullListStateDecisionMakerSubscriber implements StateSubscriberInterface
{
    public function __construct(
        private StateTransitionHandlerService $transitionHandlerService,
        private ParentItemStateService $parentItemStateService
    ) {
    }

    public function __invoke(StateSubscriberData $stateSubscriberData): void
    {
        /** @var PullListItem $pullListItem */
        $pullListItem = $stateSubscriberData->getEntityObject();

        if (PullListStatusDictionary::STOWING === $pullListItem->getStatus()) {
            return;
        }

        /** @var PullList $pullList */
        $pullList  = $pullListItem->getPullList();
        $className = get_class_name_from_object($pullList);

        $itemStatuses = $this->getPullListItemsStatus($pullList);

        $parentNextStatus = $this->parentItemStateService->findLowestStatusItems(
            PullListSortedStatusDictionary::class,
            strtoupper($className),
            $itemStatuses
        );

        if (
            ($parentNextStatus === $pullList->getStatus()) ||
            (PullListStatusDictionary::STOWING === $pullList->getStatus() &&
                PullListStatusDictionary::CONFIRMED_BY_LOCATOR === $parentNextStatus)
        ) {
            return;
        }

        $this->transitionHandlerService->transitState($pullList, $parentNextStatus);
    }

    protected function getPullListItemsStatus(PullList $pullList): array
    {
        $itemStatuses = [];
        foreach ($pullList->getItems() as $item) {
            $itemStatuses[] = $item->getStatus();
        }

        return $itemStatuses;
    }
}
