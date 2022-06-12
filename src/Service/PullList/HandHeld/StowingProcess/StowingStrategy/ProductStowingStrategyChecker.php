<?php

namespace App\Service\PullList\HandHeld\StowingProcess\StowingStrategy;

use App\Dictionary\StorageAreaStowingStrategyDictionary;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\ProductStowingStrategyException;

class ProductStowingStrategyChecker implements StowingStrategyInterface
{
    public function __construct(private ItemSerialRepository $itemSerialRepository)
    {
    }

    public function support(WarehouseStorageArea $storageArea): bool
    {
        return $storageArea->getStowingStrategy() === StorageAreaStowingStrategyDictionary::PRODUCT;
    }

    public function check(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $sameProductItemsCountWithDifferentBatch = (int) $this->itemSerialRepository->getSameProductItemsCountWithDifferentBatchInSpecificBin(
            $storageBin,
            $itemSerial->getItemBatch(),
            $itemSerial->getInventory()->getProduct()
        );

        if ($sameProductItemsCountWithDifferentBatch !== 0) {
            throw new ProductStowingStrategyException();
        }
    }
}
