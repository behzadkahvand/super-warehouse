<?php

namespace App\Service\PickList\HandHeld\Picking\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\PickList\HandHeld\Exceptions\ItemSerialStorageBinNotValidException;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;

class CheckItemSerialStorageBinResolver implements PickingResolverInterface
{
    public function resolve(PickList $pickList, ItemSerial $itemSerial): void
    {
        if ($pickList->getStorageBin()->getSerial() !== $itemSerial->getWarehouseStorageBin()->getSerial()) {
            throw new ItemSerialStorageBinNotValidException();
        }
    }

    public static function getPriority(): int
    {
        return 14;
    }
}
