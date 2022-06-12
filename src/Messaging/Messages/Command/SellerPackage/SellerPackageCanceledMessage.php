<?php

namespace App\Messaging\Messages\Command\SellerPackage;

final class SellerPackageCanceledMessage
{
    private int $sellerPackageId;

    public function setSellerPackageId(int $sellerPackageId): self
    {
        $this->sellerPackageId = $sellerPackageId;

        return $this;
    }

    public function getSellerPackageId(): int
    {
        return $this->sellerPackageId;
    }
}
