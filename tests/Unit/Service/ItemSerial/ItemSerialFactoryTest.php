<?php

namespace App\Tests\Unit\Service\ItemSerial;

use App\Entity\ItemSerial;
use App\Service\ItemSerial\ItemSerialFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class ItemSerialFactoryTest extends MockeryTestCase
{
    public function testItCanGetItemSerial(): void
    {
        $factory = new ItemSerialFactory();
        $item = $factory->create();

        self::assertInstanceOf(ItemSerial::class, $item);
    }
}
