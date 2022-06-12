<?php

namespace App\Service\PickList\HandHeld\Picking;

use App\Entity\ItemSerial;
use App\Entity\PickList;

interface PickingResolverInterface
{
    public function resolve(PickList $pickList, ItemSerial $itemSerial): void;

    public static function getPriority(): int;
}
