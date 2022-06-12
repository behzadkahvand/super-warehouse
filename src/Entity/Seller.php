<?php

namespace App\Entity;

use App\Repository\SellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="sellers")
 * @ORM\Entity(repositoryClass=SellerRepository::class)
 */
class Seller
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"seller.package.list", "seller.package.read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $mobile;

    /**
     * @ORM\OneToMany(targetEntity=SellerPackage::class, mappedBy="seller")
     */
    private $sellerPackages;

    /**
     * @ORM\OneToMany(targetEntity=WarehouseStock::class, mappedBy="seller")
     */
    private $warehouseStocks;

    public function __construct()
    {
        $this->sellerPackages = new ArrayCollection();
        $this->warehouseStocks = new ArrayCollection();
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

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getSellerPackages(): Collection
    {
        return $this->sellerPackages;
    }

    public function addSellerPackage(SellerPackage $sellerPackage): self
    {
        if (!$this->sellerPackages->contains($sellerPackage)) {
            $this->sellerPackages[] = $sellerPackage;
            $sellerPackage->setSeller($this);
        }

        return $this;
    }

    public function removeSellerPackage(SellerPackage $sellerPackage): self
    {
        if ($this->sellerPackages->removeElement($sellerPackage)) {
            // set the owning side to null (unless already changed)
            if ($sellerPackage->getSeller() === $this) {
                $sellerPackage->setSeller(null);
            }
        }

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
            $warehouseStock->setSeller($this);
        }

        return $this;
    }

    public function removeWarehouseStock(WarehouseStock $warehouseStock): self
    {
        if ($this->warehouseStocks->removeElement($warehouseStock)) {
            // set the owning side to null (unless already changed)
            if ($warehouseStock->getSeller() === $this) {
                $warehouseStock->setSeller(null);
            }
        }

        return $this;
    }
}
