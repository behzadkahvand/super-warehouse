<?php

namespace App\Service\Relocate\Stowing;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;

interface RelocateResolverInterface
{
    public function resolve(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void;

    public static function getPriority(): int;
}
