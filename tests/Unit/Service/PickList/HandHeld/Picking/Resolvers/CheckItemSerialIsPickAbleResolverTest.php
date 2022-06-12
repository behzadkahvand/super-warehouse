<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\PickList\HandHeld\Exceptions\ItemSerialNotPickableException;
use App\Service\PickList\HandHeld\Picking\Resolvers\CheckItemSerialIsPickAbleResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class CheckItemSerialIsPickAbleResolverTest extends BaseUnitTestCase
{
    public function testItCanResolve(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);
        $itemSerial->shouldReceive('getStatus')
                   ->once()
                   ->withNoArgs()
                   ->andReturn(ItemSerialStatusDictionary::SALABLE_STOCK);

        $pickList = Mockery::mock(PickList::class);

        (new CheckItemSerialIsPickAbleResolver())->resolve($pickList, $itemSerial);
    }

    public function testResolveWhenException(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);
        $itemSerial->shouldReceive('getStatus')
                   ->once()
                   ->withNoArgs()
                   ->andReturn(ItemSerialStatusDictionary::OUT_OF_STOCK);

        $pickList = Mockery::mock(PickList::class);

        self::expectException(ItemSerialNotPickableException::class);

        (new CheckItemSerialIsPickAbleResolver())->resolve($pickList, $itemSerial);
    }

    public function testPriority(): void
    {
        self::assertEquals(CheckItemSerialIsPickAbleResolver::getPriority(), 18);
    }
}
