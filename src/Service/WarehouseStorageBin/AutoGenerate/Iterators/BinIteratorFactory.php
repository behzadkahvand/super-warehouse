<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Iterators;

use App\Dictionary\StorageBinTypeDictionary;
use App\DTO\WarehouseStorageBinAutoGenerateData;
use App\Service\WarehouseStorageBin\Exceptions\BinTypeInvalidException;

final class BinIteratorFactory
{
    public function createIterator(string $type, WarehouseStorageBinAutoGenerateData $data): BinIteratorInterface|BinTypeInvalidException
    {
        return match ($type) {
            StorageBinTypeDictionary::AISLE => new AisleIterator($data),
            StorageBinTypeDictionary::BAY => new BayIterator($data),
            StorageBinTypeDictionary::CELL => new CellIterator($data),
            default => throw new BinTypeInvalidException(),
        };
    }
}