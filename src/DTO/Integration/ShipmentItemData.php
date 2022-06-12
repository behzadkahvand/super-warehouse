<?php

namespace App\DTO\Integration;

use App\DTO\BaseDTO;

final class ShipmentItemData extends BaseDTO
{
    private int $id;

    private int $inventoryId;

    private ?int $quantity;

    private string $stockType;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setInventoryId(int $inventoryId): self
    {
        $this->inventoryId = $inventoryId;

        return $this;
    }

    public function getInventoryId(): int
    {
        return $this->inventoryId;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setStockType(string $stockType): self
    {
        $this->stockType = $stockType;

        return $this;
    }

    public function getStockType(): string
    {
        return $this->stockType;
    }
}
