<?php

namespace App\Tests\Unit\Service\WarehouseStorageBin\AutoGenerate\Handlers;

use App\Dictionary\StorageBinAutoGenerationActionTypeDictionary;
use App\DTO\WarehouseStorageBinAutoGenerateData;
use App\Entity\WarehouseStorageBin;
use App\Service\WarehouseStorageBin\AutoGenerate\Creators\AisleCreator;
use App\Service\WarehouseStorageBin\AutoGenerate\Creators\BayCreator;
use App\Service\WarehouseStorageBin\AutoGenerate\Creators\CellCreator;
use App\Service\WarehouseStorageBin\AutoGenerate\Handlers\AddHandler;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class AddHandlerTest extends MockeryTestCase
{
    public function testItSupports(): void
    {
        $creators = [];
        $manager  = Mockery::mock(EntityManagerInterface::class);
        $data     = Mockery::mock(WarehouseStorageBinAutoGenerateData::class);
        $data->shouldReceive('getActionType')
             ->once()
             ->withNoArgs()
             ->andReturn(StorageBinAutoGenerationActionTypeDictionary::ADD);

        $addHandler = new AddHandler($manager, $creators);
        self::assertTrue($addHandler->supports($data));
    }

    public function testItDoesNotSupport(): void
    {
        $creators = [];
        $manager  = Mockery::mock(EntityManagerInterface::class);
        $data     = Mockery::mock(WarehouseStorageBinAutoGenerateData::class);
        $data->shouldReceive('getActionType')
             ->once()
             ->withNoArgs()
             ->andReturn(StorageBinAutoGenerationActionTypeDictionary::EDIT);

        $addHandler = new AddHandler($manager, $creators);
        self::assertFalse($addHandler->supports($data));
    }

    public function testItCanHandle(): void
    {
        $data = Mockery::mock(WarehouseStorageBinAutoGenerateData::class);

        $aisleCreator = Mockery::mock(AisleCreator::class);
        $aisleCreator->shouldReceive('supports')
                     ->once()
                     ->with($data)
                     ->andReturnFalse();
        $bayCreator = Mockery::mock(BayCreator::class);
        $bayCreator->shouldReceive('supports')
                   ->once()
                   ->with($data)
                   ->andReturnTrue();
        $bayCreator->shouldReceive('create')
                   ->once()
                   ->with($data)
                   ->andReturn([new WarehouseStorageBin()]);

        $cellCreator = Mockery::mock(CellCreator::class);
        $cellCreator->shouldReceive('supports')
                    ->once()
                    ->with($data)
                    ->andReturnTrue();
        $cellCreator->shouldReceive('create')
                    ->once()
                    ->with($data)
                    ->andReturn([new WarehouseStorageBin(), new WarehouseStorageBin()]);

        $manager = Mockery::mock(EntityManagerInterface::class);
        $manager->shouldReceive('flush')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $addHandler = new AddHandler($manager, [$aisleCreator, $bayCreator, $cellCreator]);

        self::assertCount(3, $addHandler->handle($data));
    }
}
