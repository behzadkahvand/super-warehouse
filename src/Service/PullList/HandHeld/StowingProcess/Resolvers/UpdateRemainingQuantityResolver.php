<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;

class UpdateRemainingQuantityResolver implements StowingResolverInterface
{
    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        $pullListItem->setRemainQuantity($pullListItem->getRemainQuantity() - 1);
    }

    public static function getPriority(): int
    {
        return 14;
    }
}
