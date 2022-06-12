<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Iterators;

use Iterator;

interface BinIteratorInterface extends Iterator
{
    public function toArray(): array;
}
