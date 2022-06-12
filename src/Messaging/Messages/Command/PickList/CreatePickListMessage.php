<?php

namespace App\Messaging\Messages\Command\PickList;

final class CreatePickListMessage
{
    public function __construct(private int $shipmentId)
    {
    }

    public function getShipmentId(): int
    {
        return $this->shipmentId;
    }
}
