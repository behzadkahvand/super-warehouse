<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate;

use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea as WarehouseStorageAreaAlias;

trait HelperTrait
{
    protected function formatSerial(
        string $serial,
        Warehouse $warehouse,
        WarehouseStorageAreaAlias $warehouseStorageArea
    ): string {
        return sprintf('W%dA%d-%s', $warehouse->getId(), $warehouseStorageArea->getId(), $serial);
    }

    protected function concatSerials(...$serials): string
    {
        return implode('-', $serials);
    }
}
