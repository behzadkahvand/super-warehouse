<?php

namespace App\Events\PickList;

use App\Entity\Receipt;
use App\Entity\Shipment;
use Symfony\Contracts\EventDispatcher\Event;

final class ShipmentPickListCreatedEvent extends Event implements PickListCreatedEventInterface
{
    public function __construct(private Shipment $shipment)
    {
    }

    public function getShipment(): Shipment
    {
        return $this->shipment;
    }

    public function getReceipt(): Receipt
    {
        return $this->getShipment()->getReceipt();
    }
}
