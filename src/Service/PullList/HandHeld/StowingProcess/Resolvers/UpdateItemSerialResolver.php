<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;

class UpdateItemSerialResolver implements StowingResolverInterface
{
    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        $itemSerial->setWarehouseStorageBin($storageBin)
                   ->setWarehouse($storageBin->getWarehouse())
                   ->setStatus(ItemSerialStatusDictionary::SALABLE_STOCK);
    }

    public static function getPriority(): int
    {
        return 5;
    }
}
