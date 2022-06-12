<?php

namespace App\Service\Shipment\DTO;

use App\DTO\BaseDTO;
use DateTimeInterface;

class ShipmentData extends BaseDTO
{
    private int $id;

    private ?string $status;

    private ?string $category;

    private ?DateTimeInterface $deliveryDate;

    private int $warehouseId;

    private array $items = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setDeliveryDate(?DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getDeliveryDate(): ?DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setWarehouseId(int $warehouseId): self
    {
        $this->warehouseId = $warehouseId;

        return $this;
    }

    public function getWarehouseId(): int
    {
        return $this->warehouseId;
    }
}
