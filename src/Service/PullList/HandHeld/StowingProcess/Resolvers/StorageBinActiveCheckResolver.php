<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\StorageBinNotActiveForStowException;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;

class StorageBinActiveCheckResolver implements StowingResolverInterface
{
    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        if (!$storageBin->checkIsActiveForStow()) {
            throw new StorageBinNotActiveForStowException();
        }
    }

    public static function getPriority(): int
    {
        return 25;
    }
}
