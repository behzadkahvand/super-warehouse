<?php

namespace App\DTO;

use App\Entity\Shipment;
use App\Entity\Warehouse;

class GIShipmentReceiptData
{
    private ?Shipment $shipment;

    private ?Warehouse $warehouse;

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function setShipment(?Shipment $shipment): self
    {
        $this->shipment = $shipment;
        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;
        return $this;
    }
}
