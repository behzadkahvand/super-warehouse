<?php

namespace App\Service\Inventory\DTO;

use App\DTO\BaseDTO;

class InventoryData extends BaseDTO
{
    private int $id;

    private int $productId;

    private ?string $color = null;

    private ?string $size = null;

    private ?string $guarantee = null;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setGuarantee(?string $guarantee): self
    {
        $this->guarantee = $guarantee;

        return $this;
    }

    public function getGuarantee(): ?string
    {
        return $this->guarantee;
    }
}
