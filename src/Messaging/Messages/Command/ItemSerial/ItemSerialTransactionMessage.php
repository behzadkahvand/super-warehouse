<?php

namespace App\Messaging\Messages\Command\ItemSerial;

use DateTimeInterface;

final class ItemSerialTransactionMessage
{
    private int $itemSerialId;

    private int $receiptId;

    private string $actionType;

    private ?int $warehouseId;

    private ?int $warehouseStorageBinId;

    private ?string $updatedBy;

    private DateTimeInterface $updatedAt;

    public function setItemSerialId(int $itemSerialId): self
    {
        $this->itemSerialId = $itemSerialId;

        return $this;
    }

    public function getItemSerialId(): int
    {
        return $this->itemSerialId;
    }

    public function setReceiptId(int $receiptId): self
    {
        $this->receiptId = $receiptId;

        return $this;
    }

    public function getReceiptId(): int
    {
        return $this->receiptId;
    }

    public function setActionType(string $actionType): self
    {
        $this->actionType = $actionType;

        return $this;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function setWarehouseId(?int $warehouseId): self
    {
        $this->warehouseId = $warehouseId;

        return $this;
    }

    public function getWarehouseId(): ?int
    {
        return $this->warehouseId;
    }

    public function setWarehouseStorageBinId(?int $warehouseStorageBinId): self
    {
        $this->warehouseStorageBinId = $warehouseStorageBinId;

        return $this;
    }

    public function getWarehouseStorageBinId(): ?int
    {
        return $this->warehouseStorageBinId;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }
}
