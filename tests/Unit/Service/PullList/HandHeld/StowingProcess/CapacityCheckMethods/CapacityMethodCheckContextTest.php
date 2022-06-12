<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods\CapacityMethodCheckContext;
use App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods\CapacityMethodCheckInterface;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class CapacityMethodCheckContextTest extends BaseUnitTestCase
{
    protected CapacityMethodCheckInterface|Mockery\LegacyMockInterface|Mockery\MockInterface|null $capacityMethodCheck;

    protected CapacityMethodCheckContext|null $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capacityMethodCheck = Mockery::mock(CapacityMethodCheckInterface::class);
        $this->context             = new CapacityMethodCheckContext([$this->capacityMethodCheck]);
    }

    public function testItCanCallCheckMethod(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);
        $storageBin = Mockery::mock(WarehouseStorageBin::class);

        $area = Mockery::mock(WarehouseStorageArea::class);
        $storageBin->expects('getWarehouseStorageArea')
                   ->withNoArgs()
                   ->andReturn($area);

        $this->capacityMethodCheck->expects('support')
                                  ->with($area)
                                  ->andReturnTrue();
        $this->capacityMethodCheck->expects('check')
                                  ->with($storageBin, $itemSerial)
                                  ->andReturn();

        $this->context->checkMethod($storageBin, $itemSerial);
    }
}
