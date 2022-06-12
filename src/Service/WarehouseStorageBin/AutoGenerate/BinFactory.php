<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate;

use App\Entity\WarehouseStorageBin;

final class BinFactory
{
    public function make(): WarehouseStorageBin
    {
        return new WarehouseStorageBin();
    }
}
