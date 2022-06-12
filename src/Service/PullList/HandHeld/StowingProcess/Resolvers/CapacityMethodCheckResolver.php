<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods\CapacityMethodCheckContext;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;

class CapacityMethodCheckResolver implements StowingResolverInterface
{
    public function __construct(private CapacityMethodCheckContext $checkContext)
    {
    }

    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        $this->checkContext->checkMethod($storageBin, $itemSerial);
    }

    public static function getPriority(): int
    {
        return 17;
    }
}
