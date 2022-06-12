<?php

namespace App\Messaging\Messages\Command\ItemBatch;

use DateTimeInterface;

final class ItemBatchTransactionMessage
{
    private int $itemBatchId;

    private int $receiptId;

    private int $quantity;

    private string $actionType;

    private ?int $warehouseId;

    private ?int $warehouseStorageBinId;

    private ?string $updatedBy;

    private DateTimeInterface $updatedAt;

    public function setItemBatchId(int $itemBatchId): self
    {
        $this->itemBatchId = $itemBatchId;

        return $this;
    }

    public function getItemBatchId(): int
    {
        return $this->itemBatchId;
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

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
