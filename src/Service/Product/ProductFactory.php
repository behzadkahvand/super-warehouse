<?php

namespace App\Service\Product;

use App\Entity\Product;

final class ProductFactory
{
    public function create(): Product
    {
        return new Product();
    }
}
