<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\ReceiptItem;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\ItemSerialNotInPullListException;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\ItemSerialExistInPullListCheckResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class ItemSerialExistInPullListCheckResolverTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|ItemSerialRepository|Mockery\MockInterface|null $itemSerialRepository;

    protected ItemSerialExistInPullListCheckResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemSerialRepository = Mockery::mock(ItemSerialRepository::class);

        $this->resolver = new ItemSerialExistInPullListCheckResolver($this->itemSerialRepository);
    }

    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $itemSerial1 = Mockery::mock(ItemSerial::class);
        $itemSerial1->expects('getSerial')
                    ->withNoArgs()
                    ->andReturn("test2");
        $items = [$itemSerial1];

        $this->itemSerialRepository->expects('getPullListSerials')
                                   ->with($pullList)
                                   ->andReturn($items);

        $itemSerial->expects('getSerial')
                   ->withNoArgs()
                   ->andReturn("test1");

        self::expectException(ItemSerialNotInPullListException::class);

        $this->resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(23, $this->resolver->getPriority());
    }
}
