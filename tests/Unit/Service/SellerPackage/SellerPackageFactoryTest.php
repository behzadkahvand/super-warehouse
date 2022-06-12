<?php

namespace App\Tests\Unit\Service\SellerPackage;

use App\Entity\SellerPackage;
use App\Entity\SellerPackageItem;
use App\Service\SellerPackage\SellerPackageFactory;
use App\Tests\Unit\BaseUnitTestCase;

class SellerPackageFactoryTest extends BaseUnitTestCase
{
    protected ?SellerPackageFactory $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new SellerPackageFactory();
    }

    public function testItCanGetSellerPackage(): void
    {
        self::assertInstanceOf(SellerPackage::class, $this->sut->getSellerPackage());
    }

    public function testItCanGetSellerPackageItem(): void
    {
        self::assertInstanceOf(SellerPackageItem::class, $this->sut->getSellerPackageItem());
    }
}
