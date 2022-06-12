<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\StorageBinNotActiveForStowException;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\StorageBinActiveCheckResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class StorageBinActiveCheckResolverTest extends BaseUnitTestCase
{
    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $storageBin->expects('checkIsActiveForStow')
                   ->withNoArgs()
                   ->andReturnFalse();

        $resolver = new StorageBinActiveCheckResolver();

        self::expectException(StorageBinNotActiveForStowException::class);

        $resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(25, $resolver->getPriority());
    }
}
