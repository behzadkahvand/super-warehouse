<?php

namespace App\Tests\Unit\Service\Product;

use App\Entity\Product;
use App\Service\Product\ProductFactory;
use App\Tests\Unit\BaseUnitTestCase;

final class ProductFactoryTest extends BaseUnitTestCase
{
    public function testCreate(): void
    {
        self::assertInstanceOf(Product::class, (new ProductFactory())->create());
    }
}
