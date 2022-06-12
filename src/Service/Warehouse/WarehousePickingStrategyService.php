<?php

namespace App\Service\Warehouse;

use App\Entity\Warehouse;
use App\Service\Warehouse\Exceptions\PickingStrategyNotFoundException;
use App\Service\Warehouse\PickingStrategy\PickingStrategyInterface;
use Tightenco\Collect\Support\Collection;

final class WarehousePickingStrategyService
{
    public function __construct(private iterable $strategies)
    {
    }

    public function apply(Warehouse $warehouse, Collection $data): Collection|PickingStrategyNotFoundException
    {
        /** @var PickingStrategyInterface $strategy */
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($warehouse)) {
                return $strategy->applySorting($data);
            }
        }

        throw new PickingStrategyNotFoundException();
    }
}
