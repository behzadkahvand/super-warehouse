<?php

namespace App\DTO;

use App\Entity\SellerPackage;
use App\Entity\Warehouse;

final class GRMarketPlacePackageReceiptData
{
    private ?SellerPackage $sellerPackage;

    private ?Warehouse $warehouse;

    private ?string $description;

    public function getSellerPackage(): ?SellerPackage
    {
        return $this->sellerPackage;
    }

    public function setSellerPackage(?SellerPackage $sellerPackage): void
    {
        $this->sellerPackage = $sellerPackage;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): void
    {
        $this->warehouse = $warehouse;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
