<?php

namespace App\Messaging\Messages\Event\Integration\Timcheh\Shipment;

use App\DTO\Integration\ShipmentItemData;
use DateTime;
use DateTimeInterface;

trait OrderShipmentMessageDataTrait
{
    protected int $id;

    protected ?string $status;

    protected ?string $category;

    protected ?DateTimeInterface $deliveryDate;

    protected int $warehouseId;

    protected array $items = [];

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

    public function setDeliveryDate(?string $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate ? new DateTime($deliveryDate) : null;

        return $this;
    }

    public function getDeliveryDate(): ?DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setItems(array $items): self
    {
        foreach ($items as $item) {
            $this->items[] = new ShipmentItemData($item);
        }

        return $this;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function getEntityId(): ?int
    {
        return $this->id;
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
