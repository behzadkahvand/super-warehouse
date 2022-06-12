<?php

namespace App\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\PickList\HandHeld\Exceptions\ItemSerialNotPickableException;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;

class CheckItemSerialIsPickAbleResolver implements PickingResolverInterface
{
    public function resolve(PickList $pickList, ItemSerial $itemSerial): void
    {
        if (ItemSerialStatusDictionary::SALABLE_STOCK !== $itemSerial->getStatus()) {
            throw new ItemSerialNotPickableException();
        }
    }

    public static function getPriority(): int
    {
        return 18;
    }
}
