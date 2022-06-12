<?php

namespace App\Tests\Unit\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\StowingStrategyCheckContext;
use App\Service\Relocate\Stowing\Resolvers\BinStrategyCheckResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class BinStrategyCheckResolverTest extends BaseUnitTestCase
{
    protected StowingStrategyCheckContext|Mockery\LegacyMockInterface|Mockery\MockInterface|null $stowingStrategyCheckContext;

    protected BinStrategyCheckResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stowingStrategyCheckContext = Mockery::mock(StowingStrategyCheckContext::class);
        $this->resolver                    = new BinStrategyCheckResolver($this->stowingStrategyCheckContext);
    }

    public function testItCanResolve(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $this->stowingStrategyCheckContext->expects('checkStrategy')
                                          ->with($storageBin, $itemSerial)
                                          ->andReturn();

        $this->resolver->resolve($storageBin, $itemSerial);

        self::assertEquals(18, $this->resolver::getPriority());
    }
}
