<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\UpdateRemainingQuantityResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class UpdateRemainingQuantityResolverTest extends BaseUnitTestCase
{
    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $pullListItem->expects('getRemainQuantity')
                     ->withNoArgs()
                     ->andReturn(1);
        $pullListItem->expects('setRemainQuantity')
                     ->with(0)
                     ->andReturnSelf();

        $resolver = new UpdateRemainingQuantityResolver();

        $resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(14, $resolver->getPriority());
    }
}
