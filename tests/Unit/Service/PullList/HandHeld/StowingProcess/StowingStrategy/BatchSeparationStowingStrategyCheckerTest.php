<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\StowingStrategy;

use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\BatchSeparationStowingStrategyException;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\BatchSeparationStowingStrategyChecker;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class BatchSeparationStowingStrategyCheckerTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|ItemSerialRepository|Mockery\MockInterface|null $itemSerialRepository;

    protected BatchSeparationStowingStrategyChecker|null $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemSerialRepository = Mockery::mock(ItemSerialRepository::class);

        $this->checker = new BatchSeparationStowingStrategyChecker($this->itemSerialRepository);
    }

    public function testSupport(): void
    {
        $storageArea = Mockery::mock(WarehouseStorageArea::class);
        $storageArea->expects('getStowingStrategy')
                    ->withNoArgs()
                    ->andReturn("BATCH_SEPARATION");
        self::assertTrue($this->checker->support($storageArea));
    }

    public function testCheck(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);
        $storageBin = Mockery::mock(WarehouseStorageBin::class);

        $itemBatch = Mockery::mock(ItemBatch::class);

        $itemSerial->expects('getItemBatch')
                   ->withNoArgs()
                   ->andReturn($itemBatch);

        $this->itemSerialRepository->expects('getItemsCountWithDifferentBatchInSpecificBin')
                                   ->with($storageBin, $itemBatch)
                                   ->andReturn(1);

        self::expectException(BatchSeparationStowingStrategyException::class);

        $this->checker->check($storageBin, $itemSerial);
    }
}
