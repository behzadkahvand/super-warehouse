<?php

namespace App\Tests\Unit\Service\SellerPackageItem\SellerPackageItemStatus;

use App\Entity\SellerPackageItem;
use App\Service\SellerPackageItem\SellerPackageItemStatus\SellerPackageItemPartialReceivedStatus;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class SellerPackageItemPartialReceivedStatusTest extends BaseUnitTestCase
{
    protected SellerPackageItem|LegacyMockInterface|MockInterface|null $packageItemMock;

    protected ?SellerPackageItemPartialReceivedStatus $packageItemPartialReceivedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->packageItemMock = Mockery::mock(SellerPackageItem::class);

        $this->packageItemPartialReceivedStatus = new SellerPackageItemPartialReceivedStatus();
    }

    public function testSupports(): void
    {
        $this->packageItemMock->shouldReceive('getExpectedQuantity')
                              ->once()
                              ->withNoArgs()
                              ->andReturn(2);

        $this->packageItemMock->shouldReceive('getActualQuantity')
                              ->once()
                              ->withNoArgs()
                              ->andReturn(1);

        self::assertTrue($this->packageItemPartialReceivedStatus->supports($this->packageItemMock));
    }

    public function testDoesNotSupports(): void
    {
        $this->packageItemMock->shouldReceive('getExpectedQuantity')
                              ->once()
                              ->withNoArgs()
                              ->andReturn(2);

        $this->packageItemMock->shouldReceive('getActualQuantity')
                              ->once()
                              ->withNoArgs()
                              ->andReturn(0);

        self::assertFalse($this->packageItemPartialReceivedStatus->supports($this->packageItemMock));
    }

    public function testSetStatus(): void
    {
        $this->packageItemMock->shouldReceive('setStatus')
                              ->once()
                              ->with(Mockery::type('string'))
                              ->andReturnSelf();

        self::assertInstanceOf(SellerPackageItem::class, $this->packageItemPartialReceivedStatus->setStatus($this->packageItemMock));
    }
}
