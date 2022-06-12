<?php

namespace App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods;

use App\Dictionary\StorageAreaCapacityCheckMethodDictionary;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\LackOfQuantityCapacityException;

class QuantityCapacityChecker implements CapacityMethodCheckInterface
{
    public function __construct(private ItemSerialRepository $itemSerialRepository)
    {
    }

    public function support(WarehouseStorageArea $storageArea): bool
    {
        return $storageArea->getCapacityCheckMethod() === StorageAreaCapacityCheckMethodDictionary::QUANTITY;
    }

    public function check(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $allStorageBinSerialsQuantity = (int) $this->itemSerialRepository->getStorageBinItemSerialsQuantity($storageBin);

        if ($storageBin->getQuantityCapacity() < ++$allStorageBinSerialsQuantity) {
            throw new LackOfQuantityCapacityException();
        }
    }
}
