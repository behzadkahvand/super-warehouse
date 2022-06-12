<?php

namespace App\Service\PullList\HandHeld\StowingProcess;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;

interface StowingResolverInterface
{
    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void;

    public static function getPriority(): int;
}
