<?php

namespace App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods;

use App\Dictionary\StorageAreaCapacityCheckMethodDictionary;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;

class NoneCapacityChecker implements CapacityMethodCheckInterface
{
    public function support(WarehouseStorageArea $storageArea): bool
    {
        return $storageArea->getCapacityCheckMethod() === StorageAreaCapacityCheckMethodDictionary::NONE;
    }

    public function check(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        return; // DO Noting!
    }
}
