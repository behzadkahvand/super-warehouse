<?php

namespace App\Tests\Unit\Service\SellerPackageItem\SellerPackageItemStatus;

use App\Entity\SellerPackageItem;
use App\Service\SellerPackageItem\SellerPackageItemStatus\SellerPackageItemCanceledStatus;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class SellerPackageItemCanceledStatusTest extends BaseUnitTestCase
{
    protected SellerPackageItem|LegacyMockInterface|MockInterface|null $packageItemMock;

    protected ?SellerPackageItemCanceledStatus $packageItemCanceledStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->packageItemMock = Mockery::mock(SellerPackageItem::class);

        $this->packageItemCanceledStatus = new SellerPackageItemCanceledStatus();
    }

    public function testSupports(): void
    {
        $this->packageItemMock->shouldReceive('getActualQuantity')
                              ->once()
                              ->withNoArgs()
                              ->andReturn(0);

        self::assertTrue($this->packageItemCanceledStatus->supports($this->packageItemMock));
    }

    public function testDoesNotSupports(): void
    {
        $this->packageItemMock->shouldReceive('getActualQuantity')
                              ->once()
                              ->withNoArgs()
                              ->andReturn(1);

        self::assertFalse($this->packageItemCanceledStatus->supports($this->packageItemMock));
    }

    public function testSetStatus(): void
    {
        $this->packageItemMock->shouldReceive('setStatus')
                              ->once()
                              ->with(Mockery::type('string'))
                              ->andReturnSelf();

        self::assertInstanceOf(SellerPackageItem::class, $this->packageItemCanceledStatus->setStatus($this->packageItemMock));
    }
}
