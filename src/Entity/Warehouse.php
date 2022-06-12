<?php

namespace App\Entity;

use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LongitudeOne\Spatial\PHP\Types\AbstractPoint;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Annotations\Integrationable;

/**
 * @ORM\Table(name="warehouses")
 * @ORM\Entity(repositoryClass=WarehouseRepository::class)
 */
class Warehouse
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "warehouse.list",
     *     "warehouse.read",
     *     "warehouseStorageArea.list",
     *     "warehouseStorageArea.read",
     *     "receipt.read",
     *     "warehouse.storage.bin.read",
     *     "warehouse.storage.bin.list",
     *     "warehouse.stock.list",
     *     "warehouse.stock.read",
     *     "item.serial.read",
     *     "itemBatchTransaction.read",
     *     "item.serial.transaction.read",
     *     "seller.package.read",
     *     "pullList.read",
     *     "pick.list.index",
     *     "pullList.items.add",
     *     "receipt.list",
     *     "pullList.locator.assign",
     *     "pick.list.bug.report.read",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({
     *     "warehouse.list",
     *     "warehouse.read",
     *     "warehouseStorageArea.list",
     *     "warehouseStorageArea.read",
     *     "receipt.read",
     *     "warehouse.storage.bin.read",
     *     "warehouse.storage.bin.list",
     *     "warehouse.stock.list",
     *     "warehouse.stock.read",
     *     "item.serial.transaction.list",
     *     "item.serial.transaction.read",
     *     "item.serial.list",
     *     "item.serial.read",
     *     "seller.package.list",
     *     "seller.package.read",
     *     "pullList.read",
     *     "receipt.list",
     *     "pullList.locator.assign",
     *     "pick.list.bug.report.read",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $title;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"warehouse.list", "warehouse.read"})
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"warehouse.list", "warehouse.read"})
     */
    private $trackingType;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"warehouse.list", "warehouse.read", "warehouseStorageArea.read"})
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $forSale;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"warehouse.list", "warehouse.read", "warehouseStorageArea.read"})
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $forRetailPurchase;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"warehouse.list", "warehouse.read", "warehouseStorageArea.read"})
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $forMarketPlacePurchase;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"warehouse.list", "warehouse.read", "warehouseStorageArea.read"})
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $forFmcgMarketPlacePurchase;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"warehouse.list", "warehouse.read", "warehouseStorageArea.read"})
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $forSalesReturn;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"warehouse.list", "warehouse.read", "warehouseStorageArea.read"})
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $phone;

    /**
     * @ORM\Column(type="point")
     *
     * @Groups({"warehouse.read"})
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $coordinates;

    /**
     * @ORM\Column(type="string", length=1024)
     *
     * @Groups({"warehouse.read", "warehouseStorageArea.read"})
     *
     * @Integrationable({"timcheh.warehouse.update"})
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity=Admin::class, inversedBy="warehouses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity=WarehouseStock::class, mappedBy="warehouse", orphanRemoval=true)
     */
    private $warehouseStocks;

    /**
     * @ORM\OneToMany(targetEntity=WarehouseStorageArea::class, mappedBy="warehouse", orphanRemoval=true)
     */
    private $warehouseStorageAreas;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"warehouse.list", "warehouse.read"})
     */
    private $pickingType;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"warehouse.list", "warehouse.read"})
     */
    private $pickingStrategy;

    public function __construct()
    {
        $this->warehouseStocks       = new ArrayCollection();
        $this->warehouseStorageAreas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getTrackingType(): ?string
    {
        return $this->trackingType;
    }

    public function setTrackingType(string $trackingType): self
    {
        $this->trackingType = $trackingType;

        return $this;
    }

    public function getForSale(): ?bool
    {
        return $this->forSale;
    }

    public function setForSale(bool $forSale): self
    {
        $this->forSale = $forSale;

        return $this;
    }

    public function getForRetailPurchase(): ?bool
    {
        return $this->forRetailPurchase;
    }

    public function setForRetailPurchase(bool $forRetailPurchase): self
    {
        $this->forRetailPurchase = $forRetailPurchase;

        return $this;
    }

    public function getForMarketPlacePurchase(): ?bool
    {
        return $this->forMarketPlacePurchase;
    }

    public function setForMarketPlacePurchase(bool $forMarketPlacePurchase): self
    {
        $this->forMarketPlacePurchase = $forMarketPlacePurchase;

        return $this;
    }

    public function getForFmcgMarketPlacePurchase(): ?bool
    {
        return $this->forFmcgMarketPlacePurchase;
    }

    public function setForFmcgMarketPlacePurchase(bool $forFmcgMarketPlacePurchase): self
    {
        $this->forFmcgMarketPlacePurchase = $forFmcgMarketPlacePurchase;

        return $this;
    }

    public function getForSalesReturn(): ?bool
    {
        return $this->forSalesReturn;
    }

    public function setForSalesReturn(bool $forSalesReturn): self
    {
        $this->forSalesReturn = $forSalesReturn;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCoordinates(): ?AbstractPoint
    {
        return $this->coordinates;
    }

    public function setCoordinates(AbstractPoint $coordinates): self
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getOwner(): ?Admin
    {
        return $this->owner;
    }

    public function setOwner(?Admin $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getWarehouseStocks(): Collection
    {
        return $this->warehouseStocks;
    }

    public function addWarehouseStock(WarehouseStock $warehouseStock): self
    {
        if (!$this->warehouseStocks->contains($warehouseStock)) {
            $this->warehouseStocks[] = $warehouseStock;
            $warehouseStock->setWarehouse($this);
        }

        return $this;
    }

    public function removeWarehouseStock(WarehouseStock $warehouseStock): self
    {
        if ($this->warehouseStocks->removeElement($warehouseStock)) {
            // set the owning side to null (unless already changed)
            if ($warehouseStock->getWarehouse() === $this) {
                $warehouseStock->setWarehouse(null);
            }
        }

        return $this;
    }

    public function getWarehouseStorageAreas(): Collection
    {
        return $this->warehouseStorageAreas;
    }

    public function addWarehouseStorageArea(WarehouseStorageArea $warehouseStorageArea): self
    {
        if (!$this->warehouseStorageAreas->contains($warehouseStorageArea)) {
            $this->warehouseStorageAreas[] = $warehouseStorageArea;
            $warehouseStorageArea->setWarehouse($this);
        }

        return $this;
    }

    public function removeWarehouseStorageArea(WarehouseStorageArea $warehouseStorageArea): self
    {
        if ($this->warehouseStorageAreas->removeElement($warehouseStorageArea)) {
            // set the owning side to null (unless already changed)
            if ($warehouseStorageArea->getWarehouse() === $this) {
                $warehouseStorageArea->setWarehouse(null);
            }
        }

        return $this;
    }

    public function getPickingType(): ?string
    {
        return $this->pickingType;
    }

    public function setPickingType(string $pickingType): self
    {
        $this->pickingType = $pickingType;

        return $this;
    }

    public function getPickingStrategy(): ?string
    {
        return $this->pickingStrategy;
    }

    public function setPickingStrategy(string $pickingStrategy): self
    {
        $this->pickingStrategy = $pickingStrategy;

        return $this;
    }
}
