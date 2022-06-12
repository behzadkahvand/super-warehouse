<?php

namespace App\Entity;

use App\Annotations\Integrationable;
use App\Repository\SellerPackageRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Table(name="seller_packages")
 * @ORM\Entity(repositoryClass=SellerPackageRepository::class)
 */
class SellerPackage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @Groups({"seller.package.list", "receipt.read", "seller.package.read"})
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=SellerPackageItem::class, mappedBy="sellerPackage", cascade={"persist", "remove"})
     */
    private $packageItems;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"seller.package.list", "receipt.read", "seller.package.read"})
     *
     * @Integrationable({"timcheh.seller-package.update"})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $packageType;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $productType;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"seller.package.read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Seller::class, inversedBy="packages")
     *
     * @Groups({"seller.package.list", "seller.package.read"})
     */
    private $seller;

    /**
     * @ORM\ManyToOne(targetEntity=Warehouse::class)
     *
     * @Groups({"seller.package.list", "seller.package.read"})
     */
    private $warehouse;

    public function __construct()
    {
        $this->packageItems = new ArrayCollection();
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

    public function getPackageItems(): Collection
    {
        return $this->packageItems;
    }

    public function addPackageItem(SellerPackageItem $packageItem): self
    {
        if (!$this->packageItems->contains($packageItem)) {
            $this->packageItems[] = $packageItem;
            $packageItem->setSellerPackage($this);
        }

        return $this;
    }

    public function removePackageItem(SellerPackageItem $packageItem): self
    {
        if ($this->packageItems->removeElement($packageItem)) {
            // set the owning side to null (unless already changed)
            if ($packageItem->getSellerPackage() === $this) {
                $packageItem->setSellerPackage(null);
            }
        }

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

    public function getPackageType(): ?string
    {
        return $this->packageType;
    }

    public function setPackageType(string $packageType): self
    {
        $this->packageType = $packageType;
        return $this;
    }

    public function getProductType(): ?string
    {
        return $this->productType;
    }

    public function setProductType(string $productType): self
    {
        $this->productType = $productType;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * @Groups({"seller.package.list", "seller.package.read"})
     *
     * @SerializedName("totalQuantity")
     */
    public function getTotalQuantity(): int
    {
        $quantity = 0;
        /** @var SellerPackageItem $item */
        foreach ($this->getPackageItems()->getValues() as $item) {
            $quantity += $item->getExpectedQuantity();
        }

        return $quantity;
    }

    /**
     * @Groups({"seller.package.list", "seller.package.read"})
     *
     * @SerializedName("inventoryCount")
     */
    public function getInventoryCount(): int
    {
        $inventoryIds = [];
        /** @var SellerPackageItem $item */
        foreach ($this->getPackageItems()->getValues() as $item) {
            if (!in_array($item->getInventory()->getId(), $inventoryIds)) {
                $inventoryIds[] = $item->getInventory()->getId();
            }
        }

        return count($inventoryIds);
    }

    /**
     * @Groups({"seller.package.list"})
     *
     * @SerializedName("createdAt")
     */
    public function getCreatedAtDate(): string
    {
        return $this->getCreatedAt()->format('Y-m-d');
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
}
