<?php

namespace App\Service\Inventory;

use App\Entity\Inventory;

class InventoryFactory
{
    public function create(): Inventory
    {
        return new Inventory();
    }
}
