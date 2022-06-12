<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;
use App\Service\StatusTransition\StateTransitionHandlerService;

class StowingStatusPullListResolver implements StowingResolverInterface
{
    public function __construct(private StateTransitionHandlerService $stateTransitionHandlerService)
    {
    }

    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        if (PullListStatusDictionary::STOWING !== $pullList->getStatus()) {
            $this->stateTransitionHandlerService->transitState($pullList, PullListStatusDictionary::STOWING);
        }
    }

    public static function getPriority(): int
    {
        return 11;
    }
}
