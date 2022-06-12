<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods\QuantityCapacityChecker;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\LackOfQuantityCapacityException;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class QuantityCapacityCheckerTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|ItemSerialRepository|Mockery\MockInterface|null $itemSerialRepository;

    protected QuantityCapacityChecker|null $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemSerialRepository = Mockery::mock(ItemSerialRepository::class);

        $this->checker = new QuantityCapacityChecker($this->itemSerialRepository);
    }

    public function testSupport(): void
    {
        $storageArea = Mockery::mock(WarehouseStorageArea::class);
        $storageArea->expects('getCapacityCheckMethod')
                    ->withNoArgs()
                    ->andReturn("QUANTITY");
        self::assertTrue($this->checker->support($storageArea));
    }

    public function testCheck(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);

        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $storageBin->expects('getQuantityCapacity')
                   ->withNoArgs()
                   ->andReturn(1);

        $this->itemSerialRepository->expects('getStorageBinItemSerialsQuantity')
                                   ->with($storageBin)
                                   ->andReturn(1);

        self::expectException(LackOfQuantityCapacityException::class);

        $this->checker->check($storageBin, $itemSerial);
    }
}
