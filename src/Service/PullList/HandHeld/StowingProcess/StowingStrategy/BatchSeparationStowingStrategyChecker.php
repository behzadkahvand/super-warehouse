<?php

namespace App\Service\PullList\HandHeld\StowingProcess\StowingStrategy;

use App\Dictionary\StorageAreaStowingStrategyDictionary;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Repository\WarehouseStorageBinRepository;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\BatchSeparationStowingStrategyException;

class BatchSeparationStowingStrategyChecker implements StowingStrategyInterface
{
    public function __construct(private ItemSerialRepository $itemSerialRepository)
    {
    }

    public function support(WarehouseStorageArea $storageArea): bool
    {
        return $storageArea->getStowingStrategy() === StorageAreaStowingStrategyDictionary::BATCH_SEPARATION;
    }

    public function check(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $itemsCountWithDifferentBatch = (int) $this->itemSerialRepository->getItemsCountWithDifferentBatchInSpecificBin(
            $storageBin,
            $itemSerial->getItemBatch()
        );

        if ($itemsCountWithDifferentBatch !== 0) {
            throw new BatchSeparationStowingStrategyException();
        }
    }
}
