<?php

namespace App\Tests\Unit\Service\WarehouseStorageBin\AutoGenerate\Handlers;

use App\Dictionary\StorageBinAutoGenerationActionTypeDictionary;
use App\DTO\WarehouseStorageBinAutoGenerateData;
use App\Entity\WarehouseStorageBin;
use App\Service\WarehouseStorageBin\AutoGenerate\Editors\AisleEditor;
use App\Service\WarehouseStorageBin\AutoGenerate\Editors\BayEditor;
use App\Service\WarehouseStorageBin\AutoGenerate\Editors\CellEditor;
use App\Service\WarehouseStorageBin\AutoGenerate\Handlers\EditHandler;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class EditHandlerTest extends MockeryTestCase
{
    public function testItSupports(): void
    {
        $editors = [];
        $data    = Mockery::mock(WarehouseStorageBinAutoGenerateData::class);
        $data->shouldReceive('getActionType')
             ->once()
             ->withNoArgs()
             ->andReturn(StorageBinAutoGenerationActionTypeDictionary::EDIT);

        $addHandler = new EditHandler($editors);
        self::assertTrue($addHandler->supports($data));
    }

    public function testItDoesNotSupport(): void
    {
        $editors = [];
        $data    = Mockery::mock(WarehouseStorageBinAutoGenerateData::class);
        $data->shouldReceive('getActionType')
             ->once()
             ->withNoArgs()
             ->andReturn(StorageBinAutoGenerationActionTypeDictionary::ADD);

        $addHandler = new EditHandler($editors);
        self::assertFalse($addHandler->supports($data));
    }

    public function testItCanHandle(): void
    {
        $data = Mockery::mock(WarehouseStorageBinAutoGenerateData::class);

        $aileEditor = Mockery::mock(AisleEditor::class);
        $aileEditor->shouldReceive('supports')
                   ->once()
                   ->with($data)
                   ->andReturnFalse();
        $bayEditor = Mockery::mock(BayEditor::class);
        $bayEditor->shouldReceive('supports')
                  ->once()
                  ->with($data)
                  ->andReturnFalse();

        $cellEditor = Mockery::mock(CellEditor::class);
        $cellEditor->shouldReceive('supports')
                   ->once()
                   ->with($data)
                   ->andReturnTrue();
        $cellEditor->shouldReceive('edit')
                   ->once()
                   ->with($data)
                   ->andReturn([new WarehouseStorageBin(), new WarehouseStorageBin()]);

        $addHandler = new EditHandler([$aileEditor, $bayEditor, $cellEditor]);

        self::assertCount(2, $addHandler->handle($data));
    }
}
