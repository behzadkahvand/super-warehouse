<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;
use App\Service\StatusTransition\StateTransitionHandlerService;

class StowingStatusPullListItemResolver implements StowingResolverInterface
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
        if ($pullListItem->getQuantity() - 1 === $pullListItem->getRemainQuantity()) {
            $this->stateTransitionHandlerService->transitState($pullListItem, PullListStatusDictionary::STOWING);
        }
    }

    public static function getPriority(): int
    {
        return 12;
    }
}
