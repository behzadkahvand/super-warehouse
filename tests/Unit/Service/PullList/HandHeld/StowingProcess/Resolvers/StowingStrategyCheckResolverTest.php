<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\StowingStrategyCheckResolver;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\StowingStrategyCheckContext;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\StowingStrategyInterface;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class StowingStrategyCheckResolverTest extends BaseUnitTestCase
{
    protected StowingStrategyCheckContext|Mockery\LegacyMockInterface|Mockery\MockInterface|null $stowingStrategyCheckContext;

    protected StowingStrategyCheckResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stowingStrategyCheckContext = Mockery::mock(StowingStrategyCheckContext::class);

        $this->resolver = new StowingStrategyCheckResolver($this->stowingStrategyCheckContext);
    }

    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $this->stowingStrategyCheckContext->expects('checkStrategy')
                                          ->with($storageBin, $itemSerial)
                                          ->andReturn();

        $this->resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(20, $this->resolver->getPriority());
    }
}
