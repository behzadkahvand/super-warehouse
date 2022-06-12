<?php

namespace App\Events\ItemTransaction;

use Symfony\Contracts\EventDispatcher\Event;

final class ItemBatchTransactionCreatedEvent extends Event
{
    private int $itemBatchId;

    private int $receiptId;

    private int $quantity;

    private string $actionType;

    private ?int $warehouseId;

    private ?int $warehouseStorageBinId;

    public function __construct(
        int $itemBatchId,
        int $receiptId,
        int $quantity,
        string $actionType,
        ?int $warehouseId,
        ?int $warehouseStorageBinId
    ) {
        $this->itemBatchId = $itemBatchId;
        $this->receiptId   = $receiptId;
        $this->quantity = $quantity;
        $this->actionType = $actionType;
        $this->warehouseId = $warehouseId;
        $this->warehouseStorageBinId = $warehouseStorageBinId;
    }

    public function getItemBatchId(): int
    {
        return $this->itemBatchId;
    }

    public function getReceiptId(): int
    {
        return $this->receiptId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getActionType(): string
    {
        return $this->actionType;
    }

    public function getWarehouseId(): ?int
    {
        return $this->warehouseId;
    }

    public function getWarehouseStorageBinId(): ?int
    {
        return $this->warehouseStorageBinId;
    }
}
