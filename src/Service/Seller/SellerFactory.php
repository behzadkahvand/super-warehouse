<?php

namespace App\Service\Seller;

use App\Entity\Seller;

class SellerFactory
{
    public function create(): Seller
    {
        return new Seller();
    }
}
