<?php

namespace App\Service\Shipment;

use App\Entity\Shipment;

class ShipmentFactory
{
    public function create(): Shipment
    {
        return new Shipment();
    }
}
