<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\StowingStrategy;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\StowingStrategyCheckContext;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\StowingStrategyInterface;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class StowingStrategyCheckContextTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|StowingStrategyInterface|Mockery\MockInterface|null $strategy;

    protected StowingStrategyCheckContext|null $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->strategy = Mockery::mock(StowingStrategyInterface::class);

        $this->service = new StowingStrategyCheckContext([$this->strategy]);
    }

    public function testItCanCallCheckStrategy(): void
    {
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $area = Mockery::mock(WarehouseStorageArea::class);
        $storageBin->expects('getWarehouseStorageArea')
                   ->withNoArgs()
                   ->andReturn($area);

        $this->strategy->expects('support')
                       ->with($area)
                       ->andReturnTrue();
        $this->strategy->expects('check')
                       ->with($storageBin, $itemSerial)
                       ->andReturn();

        $this->service->checkStrategy($storageBin, $itemSerial);
    }
}
