<?php

namespace App\Service\PullList\HandHeld\StowingProcess\StowingStrategy;

use App\Dictionary\StorageAreaStowingStrategyDictionary;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;

class NoneStowingStrategyChecker implements StowingStrategyInterface
{
    public function support(WarehouseStorageArea $storageArea): bool
    {
        return $storageArea->getStowingStrategy() === StorageAreaStowingStrategyDictionary::NONE;
    }

    public function check(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        return; // DO Noting!
    }
}
