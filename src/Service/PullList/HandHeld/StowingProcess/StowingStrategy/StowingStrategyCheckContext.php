<?php

namespace App\Service\PullList\HandHeld\StowingProcess\StowingStrategy;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\StowingStrategyNotFoundException;

class StowingStrategyCheckContext
{
    public function __construct(private iterable $strategies)
    {
    }

    public function checkStrategy(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $storageArea = $storageBin->getWarehouseStorageArea();

        /** @var StowingStrategyInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->support($storageArea)) {
                $strategy->check($storageBin, $itemSerial);

                return;
            }
        }

        throw new StowingStrategyNotFoundException();
    }
}
