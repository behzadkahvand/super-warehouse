<?php

namespace App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\CapacityMethodNotFoundException;

class CapacityMethodCheckContext
{
    public function __construct(private iterable $strategies)
    {
    }

    public function checkMethod(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $storageArea = $storageBin->getWarehouseStorageArea();

        /** @var CapacityMethodCheckInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($storageArea)) {
                $strategy->check($storageBin, $itemSerial);

                return;
            }
        }

        throw new CapacityMethodNotFoundException();
    }
}
