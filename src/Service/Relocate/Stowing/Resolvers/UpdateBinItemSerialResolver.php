<?php

namespace App\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\Relocate\Stowing\RelocateBinResolverInterface;
use App\Service\Relocate\Stowing\RelocateItemResolverInterface;

class UpdateBinItemSerialResolver implements RelocateItemResolverInterface, RelocateBinResolverInterface
{
    public function resolve(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $itemSerial->setWarehouseStorageBin($storageBin);
    }

    public static function getPriority(): int
    {
        return 12;
    }
}
