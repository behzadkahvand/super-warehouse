<?php

namespace App\Tests\Unit\Service\StatusTransition\Traits;

use App\DTO\AllowTransitionConfigData;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\Warehouse;
use App\Service\StatusTransition\AllowTransitions\Receipt\GRNoneReceiptAllowedTransition;
use Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ReflectionClass;

class ReceiptFactoryTransitionTest extends MockeryTestCase
{
    private Mockery\Mock|ReceiptItem|null $receiptItemMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->receiptItemMock = Mockery::mock(ReceiptItem::class)
                                        ->makePartial()
                                        ->shouldAllowMockingProtectedMethods();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->receiptItemMock = null;
        Mockery::close();
    }

    public function testReceiptFactoryTransitionWhenClassNotFound(): void
    {
        $receipt = Mockery::mock(Receipt\GRMarketPlacePackageReceipt::class);
        $this->receiptItemMock->shouldReceive('getAllowTransitionClass')
                              ->once()
                              ->with($receipt)
                              ->andReturn("App\\Test1.php");

        $reflectionClass  = new ReflectionClass($this->receiptItemMock);
        $reflectionMethod = $reflectionClass->getMethod('receiptFactoryTransition');
        $reflectionMethod->setAccessible(true);

        self::expectException(Exception::class);

        $reflectionMethod->invokeArgs(
            $this->receiptItemMock,
            [$receipt]
        );
    }

    public function testReceiptFactoryTransitionWhenClassNotValid(): void
    {
        $receipt = Mockery::mock(Receipt\GRMarketPlacePackageReceipt::class);
        $this->receiptItemMock->shouldReceive('getAllowTransitionClass')
                              ->once()
                              ->with($receipt)
                              ->andReturn(Warehouse::class);

        $reflectionClass  = new ReflectionClass($this->receiptItemMock);
        $reflectionMethod = $reflectionClass->getMethod('receiptFactoryTransition');
        $reflectionMethod->setAccessible(true);

        self::expectException(Exception::class);

        $reflectionMethod->invokeArgs(
            $this->receiptItemMock,
            [$receipt]
        );
    }

    public function testReceiptFactoryTransitionSuccessfully(): void
    {
        $receipt = Mockery::mock(Receipt\GRMarketPlacePackageReceipt::class);
        $this->receiptItemMock->shouldReceive('getAllowTransitionClass')
                              ->once()
                              ->with($receipt)
                              ->andReturn(GRNoneReceiptAllowedTransition::class);

        $reflectionClass  = new ReflectionClass($this->receiptItemMock);
        $reflectionMethod = $reflectionClass->getMethod('receiptFactoryTransition');
        $reflectionMethod->setAccessible(true);

        $object = $reflectionMethod->invokeArgs(
            $this->receiptItemMock,
            [$receipt]
        );

        self::assertInstanceOf(AllowTransitionConfigData::class, $object);
    }
}
