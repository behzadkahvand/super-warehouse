<?php

namespace App\Tests\Unit\Service\WarehouseStorageBin\AutoGenerate;

use App\Entity\WarehouseStorageBin;
use App\Service\WarehouseStorageBin\AutoGenerate\BinFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class BinFactoryTest extends MockeryTestCase
{
    public function testItCreateBin(): void
    {
        $binFactory = new BinFactory();

        self::assertInstanceOf(WarehouseStorageBin::class, $binFactory->make());
    }
}
