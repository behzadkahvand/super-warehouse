<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Editors;

use App\Dictionary\StorageBinAutoGenerationStorageLevelDictionary;
use App\Dictionary\StorageBinTypeDictionary;
use App\DTO\WarehouseStorageBinAutoGenerateData;

final class AisleEditor extends AbstractEditor
{
    public function supports(WarehouseStorageBinAutoGenerateData $data): bool
    {
        return $data->getStorageLevel() === StorageBinAutoGenerationStorageLevelDictionary::AISLE;
    }

    protected function aggregate(WarehouseStorageBinAutoGenerateData $data): array
    {
        $aisles = $this->aggregateFromDatabase($data);

        $bays = $this->mergeChildren($aisles);

        $cells = $this->mergeChildren($bays);

        return array_merge($aisles, $bays, $cells);
    }

    protected function getType(): string
    {
        return StorageBinTypeDictionary::AISLE;
    }
}
