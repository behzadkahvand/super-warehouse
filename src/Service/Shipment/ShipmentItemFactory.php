<?php

namespace App\Service\Shipment;

use App\Entity\Shipment;
use App\Entity\ShipmentItem;

class ShipmentItemFactory
{
    public function create(): ShipmentItem
    {
        return new ShipmentItem();
    }
}
