<?php

namespace App\Entity;

use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\ItemSerialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="item_serials")
 * @ORM\Entity(repositoryClass=ItemSerialRepository::class)
 */
class ItemSerial
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "itemBatch.read",
     *     "item.serial.list",
     *     "item.serial.read",
     *     "pick.hand.held.list",
     *     "receipt.list",
     *     "relocation.item",
     *     "store.st-inbound",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     *
     * @Groups({
     *     "itemBatch.read",
     *     "item.serial.list",
     *     "item.serial.read",
     *     "pick.hand.held.list",
     *     "receipt.list",
     *     "relocation.item",
     *     "store.st-inbound",
     * })
     */
    private $serial;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"item.serial.list", "item.serial.read","receipt.list","relocation.item","store.st-inbound",})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=ItemBatch::class, inversedBy="itemSerials")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"item.serial.list"})
     */
    private $itemBatch;

    /**
     * @ORM\ManyToOne(targetEntity=Inventory::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"item.serial.list", "item.serial.read", "relocation.item",})
     */
    private $inventory;

    /**
     * @ORM\ManyToOne(targetEntity=Warehouse::class)
     *
     * @Groups({"item.serial.list", "item.serial.read"})
     */
    private $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity=WarehouseStorageBin::class, inversedBy="itemSerials")
     *
     * @Groups({"item.serial.list", "item.serial.read", "relocation.item",})
     */
    private $warehouseStorageBin;

    /**
     * @ORM\OneToMany(targetEntity=ReceiptItemSerial::class, mappedBy="itemSerial")
     */
    private $receiptItemSerials;

    public function __construct()
    {
        $this->receiptItemSerials     = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(?string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getItemBatch(): ?ItemBatch
    {
        return $this->itemBatch;
    }

    public function setItemBatch(?ItemBatch $itemBatch): self
    {
        $this->itemBatch = $itemBatch;

        return $this;
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

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function getWarehouseStorageBin(): ?WarehouseStorageBin
    {
        return $this->warehouseStorageBin;
    }

    public function setWarehouseStorageBin(?WarehouseStorageBin $warehouseStorageBin): self
    {
        $this->warehouseStorageBin = $warehouseStorageBin;

        return $this;
    }

    /**
     * @return Collection|ReceiptItemSerial[]
     */
    public function getReceiptItemSerials(): Collection
    {
        return $this->receiptItemSerials;
    }

    public function addReceiptItemSerial(ReceiptItemSerial $receiptItemSerial): self
    {
        if (!$this->receiptItemSerials->contains($receiptItemSerial)) {
            $this->receiptItemSerials[] = $receiptItemSerial;
            $receiptItemSerial->setItemSerial($this);
        }

        return $this;
    }

    public function removeReceiptItemSerial(ReceiptItemSerial $receiptItemSerial): self
    {
        if ($this->receiptItemSerials->removeElement($receiptItemSerial)) {
            // set the owning side to null (unless already changed)
            if ($receiptItemSerial->getItemSerial() === $this) {
                $receiptItemSerial->setItemSerial(null);
            }
        }

        return $this;
    }
}
