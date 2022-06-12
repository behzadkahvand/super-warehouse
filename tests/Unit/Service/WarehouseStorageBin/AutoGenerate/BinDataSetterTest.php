<?php

namespace App\Tests\Unit\Service\WarehouseStorageBin\AutoGenerate;

use App\DTO\WarehouseStorageBinAutoGenerateData;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Service\WarehouseStorageBin\AutoGenerate\BinDataSetter;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class BinDataSetterTest extends MockeryTestCase
{
    public function testItCanSetData(): void
    {
        $warehouse            = Mockery::mock(Warehouse::class);
        $warehouseStorageArea = Mockery::mock(WarehouseStorageArea::class);
        $autoGenerateData     = Mockery::mock(WarehouseStorageBinAutoGenerateData::class);
        $amount               = 10;
        $autoGenerateData->shouldReceive('getHeightCapacity')
                         ->twice()
                         ->withNoArgs()
                         ->andReturn($amount);
        $autoGenerateData->shouldReceive('getWidthCapacity')
                         ->twice()
                         ->withNoArgs()
                         ->andReturn($amount);
        $autoGenerateData->shouldReceive('getWeightCapacity')
                         ->once()
                         ->withNoArgs()
                         ->andReturnNull();
        $autoGenerateData->shouldReceive('getLengthCapacity')
                         ->twice()
                         ->withNoArgs()
                         ->andReturn($amount);
        $autoGenerateData->shouldReceive('getQuantityCapacity')
                         ->twice()
                         ->withNoArgs()
                         ->andReturn($amount);
        $autoGenerateData->shouldReceive('getIsActiveForPick')
                         ->once()
                         ->withNoArgs()
                         ->andReturnFalse();
        $autoGenerateData->shouldReceive('getIsActiveForStow')
                         ->once()
                         ->withNoArgs()
                         ->andReturnTrue();
        $autoGenerateData->shouldReceive('getWarehouse')
                         ->once()
                         ->withNoArgs()
                         ->andReturn($warehouse);
        $autoGenerateData->shouldReceive('getWarehouseStorageArea')
                         ->once()
                         ->withNoArgs()
                         ->andReturn($warehouseStorageArea);

        $binObject = Mockery::mock(WarehouseStorageBin::class);
        $binObject->shouldReceive('setHeightCapacity')
                  ->once()
                  ->with($amount)
                  ->andReturn($binObject);
        $binObject->shouldReceive('setWidthCapacity')
                  ->once()
                  ->with($amount)
                  ->andReturn($binObject);
        $binObject->shouldReceive('setLengthCapacity')
                  ->once()
                  ->with($amount)
                  ->andReturn($binObject);
        $binObject->shouldReceive('setQuantityCapacity')
                  ->once()
                  ->with($amount)
                  ->andReturn($binObject);
        $binObject->shouldReceive('setIsActiveForPick')
                  ->once()
                  ->with(false)
                  ->andReturn($binObject);
        $binObject->shouldReceive('setIsActiveForStow')
                  ->once()
                  ->with(true)
                  ->andReturn($binObject);
        $binObject->shouldReceive('setWarehouse')
                  ->once()
                  ->with($warehouse)
                  ->andReturn($binObject);
        $binObject->shouldReceive('setWarehouseStorageArea')
                  ->once()
                  ->with($warehouseStorageArea)
                  ->andReturn($binObject);

        $binDataSetter = new BinDataSetter();
        $binDataSetter->setData($binObject, $autoGenerateData);
    }
}