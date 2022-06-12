<?php

namespace App\Entity;

use App\Annotations\Integrationable;
use App\Dictionary\SellerPackageItemStatusDictionary;
use App\Repository\SellerPackageItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="seller_package_items")
 * @ORM\Entity(repositoryClass=SellerPackageItemRepository::class)
 */
class SellerPackageItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @Groups({"seller.package.item.show"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SellerPackage::class, inversedBy="sellerPackageItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sellerPackage;

    /**
     * @ORM\ManyToOne(targetEntity=Inventory::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"seller.package.list", "seller.package.item.show"})
     */
    private $inventory;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"seller.package.item.show"})
     *
     * @Integrationable({"timcheh.seller-package-item.update"})
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"seller.package.list", "seller.package.item.show"})
     */
    private $expectedQuantity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Groups({"seller.package.item.show"})
     *
     * @Integrationable({"timcheh.seller-package-item.update"})
     */
    private $actualQuantity;

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSellerPackage(): ?SellerPackage
    {
        return $this->sellerPackage;
    }

    public function setSellerPackage(?SellerPackage $sellerPackage): self
    {
        $this->sellerPackage = $sellerPackage;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getExpectedQuantity(): ?int
    {
        return $this->expectedQuantity;
    }

    public function setExpectedQuantity(int $expectedQuantity): self
    {
        $this->expectedQuantity = $expectedQuantity;

        return $this;
    }

    public function getActualQuantity(): ?int
    {
        return $this->actualQuantity;
    }

    public function setActualQuantity(int $ActualQuantity): self
    {
        $this->actualQuantity = $ActualQuantity;

        return $this;
    }

    public function hasActual(): bool
    {
        return $this->getActualQuantity() > 0;
    }

    public function isReceived(): bool
    {
        return $this->getStatus() === SellerPackageItemStatusDictionary::RECEIVED;
    }

    public function isPartialReceived(): bool
    {
        return $this->getStatus() === SellerPackageItemStatusDictionary::PARTIAL_RECEIVED;
    }

    public function isCanceled(): bool
    {
        return $this->getStatus() === SellerPackageItemStatusDictionary::CANCELED;
    }
}
