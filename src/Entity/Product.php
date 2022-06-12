<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Annotations\Integrationable;

/**
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "warehouse.stock.list",
     *     "warehouse.stock.read",
     *     "product.list",
     *     "product.read",
     *     "seller.package.item.show",
     *     "pick.hand.held.list",
     *     "receipt.list",
     *     "pull-list.receipt-item.add-list",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     *     "relocation.item",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({
     *     "warehouse.stock.list",
     *     "warehouse.stock.read",
     *     "product.list",
     *     "product.read",
     *     "seller.package.item.show",
     *     "pick.hand.held.list",
     *     "receipt.list",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     *     "relocation.item",
     * })
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=WarehouseStock::class, mappedBy="product")
     */
    private $warehouseStocks;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Groups({"product.list", "product.read", "pick.hand.held.list", "relocation.item",})
     *
     * @Integrationable({"timcheh.product.update"})
     */
    private $length;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Groups({"product.list", "product.read", "pick.hand.held.list", "relocation.item",})
     *
     * @Integrationable({"timcheh.product.update"})
     */
    private $width;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Groups({"product.list", "product.read", "pick.hand.held.list", "relocation.item",})
     *
     * @Integrationable({"timcheh.product.update"})
     */
    private $height;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Groups({"product.list", "product.read", "pick.hand.held.list", "relocation.item",})
     *
     * @Integrationable({"timcheh.product.update"})
     */
    private $weight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"product.list", "product.read", "pick.hand.held.list", "relocation.item",})
     */
    private $mainImage;

    /**
     * @ORM\OneToMany(targetEntity=Inventory::class, mappedBy="product")
     */
    private $inventories;

    public function __construct()
    {
        $this->warehouseStocks = new ArrayCollection();
        $this->inventories     = new ArrayCollection();
    }

    public function setId(?int $id): self
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
            $warehouseStock->setProduct($this);
        }

        return $this;
    }

    public function removeWarehouseStock(WarehouseStock $warehouseStock): self
    {
        if ($this->warehouseStocks->removeElement($warehouseStock)) {
            // set the owning side to null (unless already changed)
            if ($warehouseStock->getProduct() === $this) {
                $warehouseStock->setProduct(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function setLength(?float $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(?float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    public function setMainImage(?string $mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @return Collection|Inventory[]
     */
    public function getInventories(): Collection
    {
        return $this->inventories;
    }

    public function addInventory(Inventory $inventory): self
    {
        if (!$this->inventories->contains($inventory)) {
            $this->inventories[] = $inventory;
            $inventory->setProduct($this);
        }

        return $this;
    }

    public function removeInventory(Inventory $inventory): self
    {
        if ($this->inventories->removeElement($inventory)) {
            // set the owning side to null (unless already changed)
            if ($inventory->getProduct() === $this) {
                $inventory->setProduct(null);
            }
        }

        return $this;
    }
}
