<?php

namespace App\Entity;

use App\Repository\InventoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="inventories")
 * @ORM\Entity(repositoryClass=InventoryRepository::class)
 */
class Inventory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "warehouse.stock.list",
     *     "warehouse.stock.read",
     *     "receipt.read",
     *     "receiptItem.read",
     *     "item.serial.read",
     *     "itemBatch.read",
     *     "seller.package.item.show",
     *     "pick.list.index",
     *     "pick.hand.held.list",
     *     "receipt.list",
     *     "pick.list.bug.report.read",
     *     "pull-list.receipt-item.add-list",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     *     "relocation.item",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "warehouse.stock.list",
     *     "warehouse.stock.read",
     *     "receipt.read",
     *     "receiptItem.read",
     *     "item.serial.list",
     *     "item.serial.read",
     *     "seller.package.item.show",
     *     "pick.hand.held.list",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     *     "relocation.item",
     * })
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity=WarehouseStock::class, mappedBy="inventory")
     */
    private $warehouseStocks;

    /**
     * @ORM\OneToMany(targetEntity=SellerPackageItem::class, mappedBy="inventory")
     */
    private $sellerPackageItems;

    /**
     * @ORM\OneToMany(targetEntity=ReceiptItem::class, mappedBy="inventory")
     */
    private $receiptItems;

    /**
     * @ORM\OneToMany(targetEntity=ItemBatch::class, mappedBy="inventory")
     */
    private $itemBatches;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "warehouse.stock.list",
     *     "warehouse.stock.read",
     *     "receipt.read",
     *     "item.serial.list",
     *     "item.serial.read",
     *     "seller.package.item.show",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     *     "relocation.item",
     * })
     */
    private $guarantee;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({
     *     "warehouse.stock.list",
     *     "warehouse.stock.read",
     *     "receipt.read",
     *     "item.serial.list",
     *     "item.serial.read",
     *     "seller.package.item.show",
     *     "pick.hand.held.list",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     *     "relocation.item",
     * })
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="inventories")
     *
     * @Groups({
     *     "seller.package.item.show",
     *     "pick.hand.held.list",
     *     "receipt.list",
     *     "pull-list.receipt-item.add-list",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     *     "relocation.item",
     * })
     */
    private $product;

    public function __construct()
    {
        $this->warehouseStocks    = new ArrayCollection();
        $this->sellerPackageItems = new ArrayCollection();
        $this->receiptItems       = new ArrayCollection();
        $this->itemBatches     = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|WarehouseStock[]
     */
    public function getWarehouseStocks(): Collection
    {
        return $this->warehouseStocks;
    }

    public function addWarehouseStock(WarehouseStock $warehouseStock): self
    {
        if (!$this->warehouseStocks->contains($warehouseStock)) {
            $this->warehouseStocks[] = $warehouseStock;
            $warehouseStock->setInventory($this);
        }

        return $this;
    }

    public function removeWarehouseStock(WarehouseStock $warehouseStock): self
    {
        if ($this->warehouseStocks->removeElement($warehouseStock)) {
            // set the owning side to null (unless already changed)
            if ($warehouseStock->getInventory() === $this) {
                $warehouseStock->setInventory(null);
            }
        }

        return $this;
    }

    public function getSellerPackageItems(): Collection
    {
        return $this->sellerPackageItems;
    }

    public function addPackage(SellerPackage $package): self
    {
        if (!$this->sellerPackageItems->contains($package)) {
            $this->sellerPackageItems[] = $package;
            $package->setInventory($this);
        }

        return $this;
    }

    public function removePackage(SellerPackage $package): self
    {
        if ($this->sellerPackageItems->removeElement($package)) {
            // set the owning side to null (unless already changed)
            if ($package->getInventory() === $this) {
                $package->setInventory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReceiptItem[]
     */
    public function getReceiptItems(): Collection
    {
        return $this->receiptItems;
    }

    public function addReceiptItem(ReceiptItem $receiptItem): self
    {
        if (!$this->receiptItems->contains($receiptItem)) {
            $this->receiptItems[] = $receiptItem;
            $receiptItem->setInventory($this);
        }

        return $this;
    }

    public function removeReceiptItem(ReceiptItem $receiptItem): self
    {
        if ($this->receiptItems->removeElement($receiptItem)) {
            // set the owning side to null (unless already changed)
            if ($receiptItem->getInventory() === $this) {
                $receiptItem->setInventory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ItemBatch[]
     */
    public function getItemBatches(): Collection
    {
        return $this->itemBatches;
    }

    public function addItemBatch(ItemBatch $itemBatch): self
    {
        if (!$this->itemBatches->contains($itemBatch)) {
            $this->itemBatches[] = $itemBatch;
            $itemBatch->setInventory($this);
        }

        return $this;
    }

    public function removeItemBatch(ItemBatch $itemBatch): self
    {
        if ($this->itemBatches->removeElement($itemBatch)) {
            // set the owning side to null (unless already changed)
            if ($itemBatch->getInventory() === $this) {
                $itemBatch->setInventory(null);
            }
        }

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getGuarantee(): ?string
    {
        return $this->guarantee;
    }

    public function setGuarantee(?string $guarantee): self
    {
        $this->guarantee = $guarantee;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
