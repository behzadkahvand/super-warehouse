<?php

namespace App\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;

class MakeItemSerialOutOfStockResolver implements PickingResolverInterface
{
    public function resolve(PickList $pickList, ItemSerial $itemSerial): void
    {
        $itemSerial->setStatus(ItemSerialStatusDictionary::OUT_OF_STOCK);
        $itemSerial->setWarehouse(null);
        $itemSerial->setWarehouseStorageBin(null);
    }

    public static function getPriority(): int
    {
        return 12;
    }
}
