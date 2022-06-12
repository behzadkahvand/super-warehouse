<?php

namespace App\Tests\Unit\Service\SellerPackage\SellerPackageStatus;

use App\Dictionary\SellerPackageStatusDictionary;
use App\Entity\SellerPackage;
use App\Entity\SellerPackageItem;
use App\Service\SellerPackage\SellerPackageStatus\SellerPackagePartialReceivedStatus;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class SellerPackagePartialReceivedStatusTest extends BaseUnitTestCase
{
    protected SellerPackageItem|LegacyMockInterface|MockInterface|null $packageItemMock;

    protected LegacyMockInterface|SellerPackage|MockInterface|null $packageMock;

    protected ?SellerPackagePartialReceivedStatus $packagePartialReceivedStatus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->packageItemMock = Mockery::mock(SellerPackageItem::class);
        $this->packageMock     = Mockery::mock(SellerPackage::class);

        $this->packagePartialReceivedStatus = new SellerPackagePartialReceivedStatus();
    }

    public function testItCanSupport(): void
    {
        $this->packageItemMock->shouldReceive('getActualQuantity')
                              ->times(3)
                              ->withNoArgs()
                              ->andReturns(5, 3, 7);

        $this->packageItemMock->shouldReceive('getExpectedQuantity')
                              ->times(3)
                              ->withNoArgs()
                              ->andReturns(5, 6, 7);

        $this->packageMock->shouldReceive('getPackageItems')
                          ->once()
                          ->withNoArgs()
                          ->andReturn(new ArrayCollection([
                              $this->packageItemMock,
                              $this->packageItemMock,
                              $this->packageItemMock,
                          ]));

        self::assertTrue($this->packagePartialReceivedStatus->supports($this->packageMock));
    }

    public function testItCanNotSupportWhenSumActualQuantitiesEqualsToZero(): void
    {
        $this->packageItemMock->shouldReceive('getActualQuantity')
                              ->times(3)
                              ->withNoArgs()
                              ->andReturns(0);

        $this->packageItemMock->shouldReceive('getExpectedQuantity')
                              ->times(3)
                              ->withNoArgs()
                              ->andReturns(5, 6, 7);

        $this->packageMock->shouldReceive('getPackageItems')
                          ->once()
                          ->withNoArgs()
                          ->andReturn(new ArrayCollection([
                              $this->packageItemMock,
                              $this->packageItemMock,
                              $this->packageItemMock,
                          ]));

        self::assertFalse($this->packagePartialReceivedStatus->supports($this->packageMock));
    }

    public function testItCanNotSupportWhenSumActualQuantitiesEqualsToSumExpectedQuantities(): void
    {
        $this->packageItemMock->shouldReceive('getActualQuantity')
                              ->times(3)
                              ->withNoArgs()
                              ->andReturns(5, 6, 7);

        $this->packageItemMock->shouldReceive('getExpectedQuantity')
                              ->times(3)
                              ->withNoArgs()
                              ->andReturns(5, 6, 7);

        $this->packageMock->shouldReceive('getPackageItems')
                          ->once()
                          ->withNoArgs()
                          ->andReturn(new ArrayCollection([
                              $this->packageItemMock,
                              $this->packageItemMock,
                              $this->packageItemMock,
                          ]));

        self::assertFalse($this->packagePartialReceivedStatus->supports($this->packageMock));
    }

    public function testItCanSetStatus(): void
    {
        $this->packageMock->expects('setStatus')
                          ->with(SellerPackageStatusDictionary::PARTIAL_RECEIVED)
                          ->andReturnSelf();

        self::assertInstanceOf(
            SellerPackage::class,
            $this->packagePartialReceivedStatus->setStatus($this->packageMock)
        );
    }
}
