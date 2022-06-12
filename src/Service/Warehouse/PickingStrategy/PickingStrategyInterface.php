<?php

namespace App\Service\Warehouse\PickingStrategy;

use App\Entity\Warehouse;
use Tightenco\Collect\Support\Collection;

interface PickingStrategyInterface
{
    public function supports(Warehouse $warehouse): bool;

    public function applySorting(Collection $data): Collection;
}
