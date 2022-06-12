<?php

namespace App\Events\SellerPackage;

use App\Entity\SellerPackage;
use Symfony\Contracts\EventDispatcher\Event;

final class SellerPackageCanceledEvent extends Event
{
    public function __construct(private SellerPackage $sellerPackage)
    {
    }

    public function getSellerPackage(): SellerPackage
    {
        return $this->sellerPackage;
    }
}
