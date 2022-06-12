<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Editors;

use App\Dictionary\StorageBinAutoGenerationStorageLevelDictionary;
use App\Dictionary\StorageBinTypeDictionary;
use App\DTO\WarehouseStorageBinAutoGenerateData;

final class CellEditor extends AbstractEditor
{
    public function supports(WarehouseStorageBinAutoGenerateData $data): bool
    {
        return $data->getStorageLevel() === StorageBinAutoGenerationStorageLevelDictionary::CELL;
    }

    protected function aggregate(WarehouseStorageBinAutoGenerateData $data): array
    {
        return $this->aggregateFromDatabase($data);
    }

    protected function getType(): string
    {
        return StorageBinTypeDictionary::CELL;
    }
}