<?php

namespace App\Events\ItemTransaction;

use Symfony\Contracts\EventDispatcher\Event;

final class ItemSerialTransactionCreatedEvent extends Event
{
    private int $itemSerialId;

    private int $receiptId;

    private string $actionType;

    private ?int $warehouseId;

    private ?int $warehouseStorageBinId;

    public function __construct(
        int $itemSerialId,
        int $receiptId,
        string $actionType,
        ?int $warehouseId,
        ?int $warehouseStorageBinId
    ) {
        $this->itemSerialId = $itemSerialId;
        $this->receiptId = $receiptId;
        $this->actionType = $actionType;
        $this->warehouseId = $warehouseId;
        $this->warehouseStorageBinId = $warehouseStorageBinId;
    }

    public function getItemSerialId(): int
    {
        return $this->itemSerialId;
    }

    public function getReceiptId(): int
    {
        return $this->receiptId;
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
