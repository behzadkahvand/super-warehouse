<?php

namespace App\Tests\Unit\Service\WarehouseStorageBin\AutoGenerate;

use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea;
use App\Service\WarehouseStorageBin\AutoGenerate\HelperTrait;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class HelperTraitTest extends MockeryTestCase
{
    use HelperTrait;

    public function testFormatSerial(): void
    {
        $serial                 = 'AA-A0-00';
        $warehouseId            = 1;
        $warehouseStorageAreaId = 1;

        $warehouseMock = Mockery::mock(Warehouse::class);
        $warehouseMock->shouldReceive('getId')
                      ->once()
                      ->withNoArgs()
                      ->andReturn($warehouseId);

        $warehouseStorageAreaMock = Mockery::mock(WarehouseStorageArea::class);
        $warehouseStorageAreaMock->shouldReceive('getId')
                                 ->once()
                                 ->withNoArgs()
                                 ->andReturn($warehouseStorageAreaId);

        self::assertEquals(
            sprintf('W%dA%d-%s', $warehouseId, $warehouseStorageAreaId, $serial),
            $this->formatSerial($serial, $warehouseMock, $warehouseStorageAreaMock)
        );
    }

    public function testConcatSerials(): void
    {
        $serial1 = 'AA';
        $serial2 = 'A0';
        $serial3 = '00';

        self::assertEquals('AA-A0-00', $this->concatSerials($serial1, $serial2, $serial3));
    }
}
