<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\PickList\HandHeld\Picking\Resolvers\MakeItemSerialOutOfStockResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class MakeItemSerialOutOfStockResolverTest extends BaseUnitTestCase
{
    public function testItCanResolve(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);
        $itemSerial->shouldReceive('setStatus')
                   ->once()
                   ->with(ItemSerialStatusDictionary::OUT_OF_STOCK)
                   ->andReturnSelf();
        $itemSerial->shouldReceive('setWarehouse')
                   ->once()
                   ->with(null)
                   ->andReturnSelf();
        $itemSerial->shouldReceive('setWarehouseStorageBin')
                   ->once()
                   ->with(null)
                   ->andReturnSelf();

        $pickList = Mockery::mock(PickList::class);

        (new MakeItemSerialOutOfStockResolver())->resolve($pickList, $itemSerial);
    }

    public function testPriority(): void
    {
        self::assertEquals(MakeItemSerialOutOfStockResolver::getPriority(), 12);
    }
}
