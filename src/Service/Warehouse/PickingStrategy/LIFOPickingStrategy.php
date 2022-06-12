<?php

namespace App\Service\Warehouse\PickingStrategy;

use App\Dictionary\WarehousePickingStrategyDictionary;
use App\Entity\Warehouse;
use Tightenco\Collect\Support\Collection;

final class LIFOPickingStrategy implements PickingStrategyInterface
{
    public function supports(Warehouse $warehouse): bool
    {
        return $warehouse->getPickingStrategy() === WarehousePickingStrategyDictionary::LIFO;
    }

    public function applySorting(Collection $data): Collection
    {
        return $data->sortByDesc(fn($item) => $item[0]->getCreatedAt());
    }
}
