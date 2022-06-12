<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\ReceiptItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\ItemSerialStowDuplicationException;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\ItemSerialDuplicationCheckResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class ItemSerialDuplicationCheckResolverTest extends BaseUnitTestCase
{
    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $itemSerial->expects('getStatus')
                   ->withNoArgs()
                   ->andReturn(ItemSerialStatusDictionary::SALABLE_STOCK);

        $resolver = new ItemSerialDuplicationCheckResolver();

        self::expectException(ItemSerialStowDuplicationException::class);

        $resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(30, $resolver->getPriority());
    }
}
