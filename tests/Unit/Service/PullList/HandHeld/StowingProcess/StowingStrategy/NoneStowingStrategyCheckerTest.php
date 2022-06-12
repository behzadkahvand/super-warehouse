<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\StowingStrategy;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\NoneStowingStrategyChecker;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class NoneStowingStrategyCheckerTest extends BaseUnitTestCase
{
    public function testSupport(): void
    {
        $storageArea = Mockery::mock(WarehouseStorageArea::class);
        $storageArea->expects('getStowingStrategy')
                    ->withNoArgs()
                    ->andReturn("NONE");
        self::assertTrue((new NoneStowingStrategyChecker())->support($storageArea));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCheck(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        (new NoneStowingStrategyChecker())->check($storageBin, $itemSerial);
    }
}
