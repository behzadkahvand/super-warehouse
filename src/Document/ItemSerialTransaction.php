<?php

namespace App\Document;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="item_serial_transactions")
 */
class ItemSerialTransaction
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(name="warehouse_id",type="int")
     */
    protected $warehouseId;

    /**
     * @MongoDB\Field(name="warehouse_storage_bin_id",type="int")
     */
    protected $warehouseStorageBinId;

    /**
     * @MongoDB\Field(name="receipt_id",type="int")
     */
    protected $receiptId;

    /**
     * @MongoDB\Field(name="item_serial_id",type="int")
     */
    protected $itemSerialId;

    /**
     * @MongoDB\Field(name="action_type",type="string")
     */
    protected $actionType;

    /**
     * @MongoDB\Field(name="updated_by",type="int")
     */
    protected $updatedBy;

    /**
     * @MongoDB\Field(name="updated_at",type="date")
     */
    protected $updatedAt;


    public function getId()
    {
        return $this->id;
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

    public function setReceiptId(?int $receiptId): self
    {
        $this->receiptId = $receiptId;

        return $this;
    }

    public function getReceiptId(): ?int
    {
        return $this->receiptId;
    }

    public function setItemSerialId(int $itemSerialId): self
    {
        $this->itemSerialId = $itemSerialId;

        return $this;
    }

    public function getItemSerialId(): int
    {
        return $this->itemSerialId;
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

    public function setUpdatedBy(?int $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?int
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
