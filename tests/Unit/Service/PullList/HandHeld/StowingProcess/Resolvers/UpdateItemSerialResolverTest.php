<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\UpdateItemSerialResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class UpdateItemSerialResolverTest extends BaseUnitTestCase
{
    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $warehouse = Mockery::mock(Warehouse::class);
        $storageBin->expects('getWarehouse')
                   ->withNoArgs()
                   ->andReturn($warehouse);

        $itemSerial->expects('setWarehouseStorageBin')
                   ->with($storageBin)
                   ->andReturnSelf();
        $itemSerial->expects('setWarehouse')
                   ->with($warehouse)
                   ->andReturnSelf();
        $itemSerial->expects('setStatus')
                   ->with(ItemSerialStatusDictionary::SALABLE_STOCK)
                   ->andReturnSelf();

        $resolver = new UpdateItemSerialResolver();

        $resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(5, $resolver->getPriority());
    }
}
