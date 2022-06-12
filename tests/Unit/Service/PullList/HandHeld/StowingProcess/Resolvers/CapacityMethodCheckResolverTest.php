<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\CapacityCheckMethods\CapacityMethodCheckContext;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\CapacityMethodCheckResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class CapacityMethodCheckResolverTest extends BaseUnitTestCase
{
    protected CapacityMethodCheckContext|Mockery\LegacyMockInterface|Mockery\MockInterface|null $capacityMethodCheckContext;

    protected CapacityMethodCheckResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capacityMethodCheckContext = Mockery::mock(CapacityMethodCheckContext::class);
        $this->resolver                   = new CapacityMethodCheckResolver($this->capacityMethodCheckContext);
    }

    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $this->capacityMethodCheckContext->expects('checkMethod')
                                         ->with($storageBin, $itemSerial)
                                         ->andReturn();

        $this->resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(17, $this->resolver->getPriority());
    }
}
