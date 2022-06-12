<?php

namespace App\Tests\Unit\Service\Seller;

use App\Entity\Seller;
use App\Service\Seller\SellerFactory;
use App\Tests\Unit\BaseUnitTestCase;

class SellerFactoryTest extends BaseUnitTestCase
{
    public function testItCanCreateSellerObject(): void
    {
        self::assertInstanceOf(Seller::class, (new SellerFactory())->create());
    }
}
