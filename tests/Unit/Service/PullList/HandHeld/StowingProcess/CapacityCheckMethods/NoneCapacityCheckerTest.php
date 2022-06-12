<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods\NoneCapacityChecker;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class NoneCapacityCheckerTest extends BaseUnitTestCase
{
    public function testSupport(): void
    {
        $storageArea = Mockery::mock(WarehouseStorageArea::class);
        $storageArea->expects('getCapacityCheckMethod')
                    ->withNoArgs()
                    ->andReturn("NONE");
        self::assertTrue((new NoneCapacityChecker())->support($storageArea));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCheck(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial  = Mockery::mock(ItemSerial::class);

        (new NoneCapacityChecker())->check($storageBin, $itemSerial);
    }
}
