<?php

namespace App\DTO\Integration;

use App\DTO\BaseDTO;

final class SellerPackageItemData extends BaseDTO
{
    private int $id;

    private int $inventoryId;

    private string $status;

    private int $quantity;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getInventoryId(): int
    {
        return $this->inventoryId;
    }

    public function setInventoryId(int $inventoryId): self
    {
        $this->inventoryId = $inventoryId;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }
}
