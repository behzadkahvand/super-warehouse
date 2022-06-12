<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\StowingStrategyCheckContext;

class StowingStrategyCheckResolver implements StowingResolverInterface
{
    public function __construct(private StowingStrategyCheckContext $checkContext)
    {
    }

    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        $this->checkContext->checkStrategy($storageBin, $itemSerial);
    }

    public static function getPriority(): int
    {
        return 20;
    }
}
