<?php

namespace App\Service\Shipment\DTO;

use App\DTO\BaseDTO;
use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\SourceShipmentData;
use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\TargetShipmentData;

class ShipmentCloneData extends BaseDTO
{
    private ?SourceShipmentData $sourceShipment;

    private ?TargetShipmentData $targetShipment;

    public function setSourceShipment(array $sourceShipment): self
    {
        $this->sourceShipment = new SourceShipmentData($sourceShipment);

        return $this;
    }

    public function getSourceShipment(): SourceShipmentData
    {
        return $this->sourceShipment;
    }

    public function setTargetShipment(array $targetShipment): self
    {
        $this->targetShipment = new TargetShipmentData($targetShipment);

        return $this;
    }

    public function getTargetShipment(): TargetShipmentData
    {
        return $this->targetShipment;
    }
}
