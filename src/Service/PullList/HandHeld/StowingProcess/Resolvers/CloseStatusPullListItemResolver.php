<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;
use App\Service\StatusTransition\StateTransitionHandlerService;

class CloseStatusPullListItemResolver implements StowingResolverInterface
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
        if (0 === $pullListItem->getRemainQuantity()) {
            $this->stateTransitionHandlerService->transitState($pullListItem, PullListStatusDictionary::CLOSED);
        }
    }

    public static function getPriority(): int
    {
        return 10;
    }
}
