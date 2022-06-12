<?php

namespace App\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\StorageBinNotActiveForStowException;
use App\Service\Relocate\Stowing\RelocateItemResolverInterface;

class CheckStorageBinIsActiveResolver implements RelocateItemResolverInterface
{
    public function resolve(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        if (!$storageBin->checkIsActiveForStow()) {
            throw new StorageBinNotActiveForStowException();
        }
    }

    public static function getPriority(): int
    {
        return 20;
    }
}
