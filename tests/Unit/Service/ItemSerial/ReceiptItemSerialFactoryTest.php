<?php

namespace App\Tests\Unit\Service\ItemSerial;

use App\Entity\ReceiptItemSerial;
use App\Service\ItemSerial\ReceiptItemSerialFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class ReceiptItemSerialFactoryTest extends MockeryTestCase
{
    public function testItCanGetItemSerial(): void
    {
        $factory = new ReceiptItemSerialFactory();
        $item = $factory->create();

        self::assertInstanceOf(ReceiptItemSerial::class, $item);
    }
}
