<?php

namespace App\Tests\Unit\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods\CapacityMethodCheckContext;
use App\Service\Relocate\Stowing\Resolvers\BinCapacityCheckResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class BinCapacityCheckResolverTest extends BaseUnitTestCase
{
    protected CapacityMethodCheckContext|Mockery\LegacyMockInterface|Mockery\MockInterface|null $capacityMethodCheckContext;

    protected BinCapacityCheckResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capacityMethodCheckContext = Mockery::mock(CapacityMethodCheckContext::class);
        $this->resolver                   = new BinCapacityCheckResolver($this->capacityMethodCheckContext);
    }

    public function testItCanResolve(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $this->capacityMethodCheckContext->expects('checkMethod')
            ->with($storageBin, $itemSerial)
            ->andReturn();

        $this->resolver->resolve($storageBin, $itemSerial);
        self::assertEquals(15, $this->resolver::getPriority());
    }
}
