<?php

namespace App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;

interface CapacityMethodCheckInterface
{
    public function support(WarehouseStorageArea $storageArea): bool;

    public function check(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void;
}
