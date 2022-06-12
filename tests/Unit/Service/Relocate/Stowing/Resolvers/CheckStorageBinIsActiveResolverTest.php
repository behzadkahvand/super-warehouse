<?php

namespace App\Tests\Unit\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\StorageBinNotActiveForStowException;
use App\Service\Relocate\Stowing\Resolvers\CheckStorageBinIsActiveResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class CheckStorageBinIsActiveResolverTest extends BaseUnitTestCase
{
    protected CheckStorageBinIsActiveResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new CheckStorageBinIsActiveResolver();
    }

    public function testItCanResolve(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $storageBin->expects('checkIsActiveForStow')
                   ->withNoArgs()
                   ->andReturnFalse();

        self::expectException(StorageBinNotActiveForStowException::class);

        $this->resolver->resolve($storageBin, $itemSerial);

        self::assertEquals(20, $this->resolver::getPriority());
    }
}
