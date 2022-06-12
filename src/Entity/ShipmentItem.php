<?php

namespace App\Entity;

use App\Dictionary\ShipmentItemStockTypeDictionary;
use App\Repository\ShipmentItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="shipment_items")
 * @ORM\Entity(repositoryClass=ShipmentItemRepository::class)
 */
class ShipmentItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Inventory::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $inventory;

    /**
     * @ORM\ManyToOne(targetEntity=Shipment::class, inversedBy="shipmentItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shipment;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=15, nullable=true, options={"default"="SELLER"})
     */
    private $stockType = ShipmentItemStockTypeDictionary::SELLER;

    /**
     * @ORM\OneToOne(targetEntity=ReceiptItem::class, cascade={"persist", "remove"})
     */
    private $receiptItem;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setInventory(?Inventory $inventory): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    public function setShipment(?Shipment $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function setStockType(?string $stockType): self
    {
        $this->stockType = $stockType;

        return $this;
    }

    public function getStockType(): ?string
    {
        return $this->stockType;
    }

    public function getReceiptItem(): ?ReceiptItem
    {
        return $this->receiptItem;
    }

    public function setReceiptItem(?ReceiptItem $receiptItem): self
    {
        $this->receiptItem = $receiptItem;

        return $this;
    }
}
