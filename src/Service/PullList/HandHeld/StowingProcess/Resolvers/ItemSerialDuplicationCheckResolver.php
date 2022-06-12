<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\ItemSerialStowDuplicationException;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;

class ItemSerialDuplicationCheckResolver implements StowingResolverInterface
{
    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        if (ItemSerialStatusDictionary::SALABLE_STOCK === $itemSerial->getStatus()) {
            throw new ItemSerialStowDuplicationException();
        }
    }

    public static function getPriority(): int
    {
        return 30;
    }
}
