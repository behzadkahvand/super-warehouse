<?php

namespace App\Tests\Unit\Service\SellerPackageItem\SellerPackageItemStatus;

use App\Entity\SellerPackageItem;
use App\Service\SellerPackageItem\SellerPackageItemStatus\SellerPackageItemReceivedStatus;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class SellerPackageItemReceivedStatusTest extends BaseUnitTestCase
{
    protected SellerPackageItem|LegacyMockInterface|MockInterface|null $packageItemMock;

    protected ?SellerPackageItemReceivedStatus $packageItemReceivedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->packageItemMock = Mockery::mock(SellerPackageItem::class);

        $this->packageItemReceivedStatus = new SellerPackageItemReceivedStatus();
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
                    ->andReturn(2);

        self::assertTrue($this->packageItemReceivedStatus->supports($this->packageItemMock));
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
                    ->andReturn(1);

        self::assertFalse($this->packageItemReceivedStatus->supports($this->packageItemMock));
    }

    public function testSetStatus(): void
    {
        $this->packageItemMock->shouldReceive('setStatus')
                    ->once()
                    ->with(Mockery::type('string'))
                    ->andReturnSelf();

        self::assertInstanceOf(SellerPackageItem::class, $this->packageItemReceivedStatus->setStatus($this->packageItemMock));
    }
}
