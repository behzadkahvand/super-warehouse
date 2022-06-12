<?php

namespace App\Entity;

use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\WarehouseStorageBinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="warehouse_storage_bins")
 * @ORM\Entity(repositoryClass=WarehouseStorageBinRepository::class)
 */
class WarehouseStorageBin
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "warehouse.storage.bin.read",
     *     "warehouse.storage.bin.list",
     *     "itemBatchTransaction.read",
     *     "item.serial.transaction.read",
     *     "item.serial.read",
     *     "itemBatchTransaction.read",
     *     "pick.list.index",
     *     "pick.list.bug.report.read",
     *     "pick.hand.held.list",
     *     "stow.hand-held.scan-serial",
     *     "relocation.item",
     *     "relocation.bin",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({
     *     "warehouse.storage.bin.read",
     *     "warehouse.storage.bin.list",
     *     "item.serial.transaction.list",
     *     "item.serial.transaction.read",
     *     "stow.hand-held.scan-serial",
     *     "relocation.item",
     *     "relocation.bin",
     * })
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Groups({
     *     "warehouse.storage.bin.read",
     *     "warehouse.storage.bin.list",
     *     "item.serial.read",
     *     "item.serial.list",
     *     "item.serial.transaction.list",
     *     "item.serial.transaction.read",
     *     "pick.list.bug.report.read",
     *     "pick.hand.held.list",
     *      "stow.hand-held.scan-serial",
     *     "relocation.item",
     *     "relocation.bin",
     * })
     */
    private $serial;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"warehouse.storage.bin.read", "warehouse.storage.bin.list", "relocation.bin",})
     */
    private $isActiveForStow;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"warehouse.storage.bin.read", "warehouse.storage.bin.list", "relocation.bin",})
     */
    private $isActiveForPick;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"warehouse.storage.bin.read", "warehouse.storage.bin.list"})
     */
    private $quantityCapacity;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"warehouse.storage.bin.read", "warehouse.storage.bin.list"})
     */
    private $widthCapacity;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"warehouse.storage.bin.read", "warehouse.storage.bin.list"})
     */
    private $heightCapacity;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"warehouse.storage.bin.read", "warehouse.storage.bin.list"})
     */
    private $lengthCapacity;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"warehouse.storage.bin.read", "warehouse.storage.bin.list"})
     */
    private $weightCapacity;

    /**
     * @ORM\ManyToOne(targetEntity=Warehouse::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"warehouse.storage.bin.read", "warehouse.storage.bin.list"})
     */
    private $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity=WarehouseStorageArea::class, inversedBy="warehouseStorageBins")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"warehouse.storage.bin.read", "warehouse.storage.bin.list"})
     */
    private $warehouseStorageArea;

    /**
     * @ORM\ManyToOne(targetEntity=WarehouseStorageBin::class, inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"warehouse.storage.bin.read"})
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=WarehouseStorageBin::class, mappedBy="parent")
     *
     * @Groups({"warehouse.storage.bin.read"})
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity=ItemSerial::class, mappedBy="warehouseStorageBin")
     */
    private $itemSerials;

    public function __construct()
    {
        $this->children    = new ArrayCollection();
        $this->itemSerials = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    public function getIsActiveForStow(): ?bool
    {
        return $this->isActiveForStow;
    }

    public function setIsActiveForStow(bool $isActiveForStow): self
    {
        $this->isActiveForStow = $isActiveForStow;

        return $this;
    }

    public function getIsActiveForPick(): ?bool
    {
        return $this->isActiveForPick;
    }

    public function setIsActiveForPick(bool $isActiveForPick): self
    {
        $this->isActiveForPick = $isActiveForPick;

        return $this;
    }

    public function getQuantityCapacity(): ?int
    {
        return $this->quantityCapacity;
    }

    public function setQuantityCapacity(int $quantityCapacity): self
    {
        $this->quantityCapacity = $quantityCapacity;

        return $this;
    }

    public function getWidthCapacity(): ?int
    {
        return $this->widthCapacity;
    }

    public function setWidthCapacity(int $widthCapacity): self
    {
        $this->widthCapacity = $widthCapacity;

        return $this;
    }

    public function getHeightCapacity(): ?int
    {
        return $this->heightCapacity;
    }

    public function setHeightCapacity(int $heightCapacity): self
    {
        $this->heightCapacity = $heightCapacity;

        return $this;
    }

    public function getLengthCapacity(): ?int
    {
        return $this->lengthCapacity;
    }

    public function setLengthCapacity(int $lengthCapacity): self
    {
        $this->lengthCapacity = $lengthCapacity;

        return $this;
    }

    public function getWeightCapacity(): ?int
    {
        return $this->weightCapacity;
    }

    public function setWeightCapacity(int $weightCapacity): self
    {
        $this->weightCapacity = $weightCapacity;

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

    public function getWarehouseStorageArea(): ?WarehouseStorageArea
    {
        return $this->warehouseStorageArea;
    }

    public function setWarehouseStorageArea(?WarehouseStorageArea $warehouseStorageArea): self
    {
        $this->warehouseStorageArea = $warehouseStorageArea;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ItemSerial[]
     */
    public function getItemSerials(): Collection
    {
        return $this->itemSerials;
    }

    public function addItemSerial(ItemSerial $itemSerial): self
    {
        if (!$this->itemSerials->contains($itemSerial)) {
            $this->itemSerials[] = $itemSerial;
            $itemSerial->setWarehouseStorageBin($this);
        }

        return $this;
    }

    public function removeItemSerial(ItemSerial $itemSerial): self
    {
        if ($this->itemSerials->removeElement($itemSerial)) {
            // set the owning side to null (unless already changed)
            if ($itemSerial->getWarehouseStorageBin() === $this) {
                $itemSerial->setWarehouseStorageBin(null);
            }
        }

        return $this;
    }

    public function checkIsActiveForStow(): bool
    {
        $warehouseStorageArea = $this->getWarehouseStorageArea();

        return $this->getIsActiveForStow() && $warehouseStorageArea->getIsActive() &&
            $warehouseStorageArea->getWarehouse()->getIsActive();
    }
}
