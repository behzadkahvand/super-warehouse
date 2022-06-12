<?php

namespace App\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods\CapacityMethodCheckContext;
use App\Service\Relocate\Stowing\RelocateBinResolverInterface;
use App\Service\Relocate\Stowing\RelocateItemResolverInterface;

class BinCapacityCheckResolver implements RelocateItemResolverInterface, RelocateBinResolverInterface
{
    public function __construct(private CapacityMethodCheckContext $checkContext)
    {
    }

    public function resolve(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $this->checkContext->checkMethod($storageBin, $itemSerial);
    }

    public static function getPriority(): int
    {
        return 15;
    }
}
