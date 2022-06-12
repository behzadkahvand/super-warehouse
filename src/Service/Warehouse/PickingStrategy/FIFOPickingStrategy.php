<?php

namespace App\Service\Warehouse\PickingStrategy;

use App\Dictionary\WarehousePickingStrategyDictionary;
use App\Entity\Warehouse;
use Tightenco\Collect\Support\Collection;

final class FIFOPickingStrategy implements PickingStrategyInterface
{
    public function supports(Warehouse $warehouse): bool
    {
        return $warehouse->getPickingStrategy() === WarehousePickingStrategyDictionary::FIFO;
    }

    public function applySorting(Collection $data): Collection
    {
        return $data->sortBy(fn($item) => $item[0]->getCreatedAt());
    }
}
