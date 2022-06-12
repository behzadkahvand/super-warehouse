<?php

namespace App\Tests\Unit\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\Relocate\Stowing\Resolvers\UpdateBinItemSerialResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class UpdateBinItemSerialResolverTest extends BaseUnitTestCase
{
    protected UpdateBinItemSerialResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new UpdateBinItemSerialResolver();
    }

    public function testItCanResolve(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $itemSerial->expects('setWarehouseStorageBin')
                   ->with($storageBin)
                   ->andReturnSelf();

        $this->resolver->resolve($storageBin, $itemSerial);

        self::assertEquals(12, $this->resolver::getPriority());
    }
}
