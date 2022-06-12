<?php

namespace App\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\StowingStrategyCheckContext;
use App\Service\Relocate\Stowing\RelocateBinResolverInterface;
use App\Service\Relocate\Stowing\RelocateItemResolverInterface;

class BinStrategyCheckResolver implements RelocateItemResolverInterface, RelocateBinResolverInterface
{
    public function __construct(private StowingStrategyCheckContext $checkContext)
    {
    }

    public function resolve(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $this->checkContext->checkStrategy($storageBin, $itemSerial);
    }

    public static function getPriority(): int
    {
        return 18;
    }
}
