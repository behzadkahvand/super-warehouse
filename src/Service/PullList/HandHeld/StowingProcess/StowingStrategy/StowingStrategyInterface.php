<?php

namespace App\Service\PullList\HandHeld\StowingProcess\StowingStrategy;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;

interface StowingStrategyInterface
{
    public function support(WarehouseStorageArea $storageArea): bool;

    public function check(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void;
}
