<?php

namespace App\Service\Warehouse\PickingStrategy;

use App\Dictionary\WarehousePickingStrategyDictionary;
use App\Entity\Warehouse;
use Tightenco\Collect\Support\Collection;

final class FEFOPickingStrategy implements PickingStrategyInterface
{
    public function supports(Warehouse $warehouse): bool
    {
        return $warehouse->getPickingStrategy() === WarehousePickingStrategyDictionary::FEFO;
    }

    public function applySorting(Collection $data): Collection
    {
        return $data->sortBy(fn($item) => $item[0]->getItemBatch()->getExpireAt());
    }
}
