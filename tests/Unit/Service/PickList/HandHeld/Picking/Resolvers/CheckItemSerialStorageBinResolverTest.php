<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Entity\WarehouseStorageBin;
use App\Service\PickList\HandHeld\Exceptions\ItemSerialStorageBinNotValidException;
use App\Service\PickList\HandHeld\Picking\Resolvers\CheckItemSerialStorageBinResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class CheckItemSerialStorageBinResolverTest extends BaseUnitTestCase
{
    public function testItCanResolve(): void
    {
        $bin = Mockery::mock(WarehouseStorageBin::class);
        $bin->shouldReceive('getSerial')
            ->twice()
            ->withNoArgs()
            ->andReturn("test");

        $itemSerial = Mockery::mock(ItemSerial::class);
        $itemSerial->expects('getWarehouseStorageBin')
                 ->withNoArgs()
                 ->andReturn($bin);

        $pickList = Mockery::mock(PickList::class);
        $pickList->expects('getStorageBin')
                 ->withNoArgs()
                 ->andReturn($bin);

        (new CheckItemSerialStorageBinResolver())->resolve($pickList, $itemSerial);
    }

    public function testResolveWhenException(): void
    {
        $bin = Mockery::mock(WarehouseStorageBin::class);
        $bin->shouldReceive('getSerial')
            ->twice()
            ->withNoArgs()
            ->andReturn("test1", "test2");

        $itemSerial = Mockery::mock(ItemSerial::class);
        $itemSerial->expects('getWarehouseStorageBin')
                   ->withNoArgs()
                   ->andReturn($bin);

        $pickList = Mockery::mock(PickList::class);
        $pickList->expects('getStorageBin')
                 ->withNoArgs()
                 ->andReturn($bin);

        self::expectException(ItemSerialStorageBinNotValidException::class);

        (new CheckItemSerialStorageBinResolver())->resolve($pickList, $itemSerial);
    }

    public function testPriority(): void
    {
        self::assertEquals(CheckItemSerialStorageBinResolver::getPriority(), 14);
    }
}
