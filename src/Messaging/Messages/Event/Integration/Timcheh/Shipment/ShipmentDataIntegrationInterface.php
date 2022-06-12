<?php

namespace App\Messaging\Messages\Event\Integration\Timcheh\Shipment;

interface ShipmentDataIntegrationInterface
{
    public function setId(int $id): self;

    public function setStatus(?string $status): self;

    public function setCategory(?string $category): self;

    public function setDeliveryDate(?string $deliveryDate): self;

    public function setItems(array $items): self;

    public function setWarehouseId(int $warehouseId): self;
}
