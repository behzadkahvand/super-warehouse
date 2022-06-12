<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\PullListPriorityDictionary;
use App\Dictionary\PullListStatusDictionary;
use App\Dictionary\StorageBinTypeDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\WarehouseStorageBin;
use App\Repository\PullListRepository;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\Persistence\ObjectRepository;

class RelocationControllerTest extends FunctionalTestCase
{
    protected ObjectRepository|null $itemSerialRepository;

    protected ObjectRepository|null $storageBinRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemSerialRepository = $this->manager->getRepository(ItemSerial::class);
        $this->storageBinRepository = $this->manager->getRepository(WarehouseStorageBin::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->itemSerialRepository = null;
        $this->storageBinRepository = null;
    }

    public function testPickItemSuccess(): void
    {
        /** @var ItemSerial $itemSerial */
        $itemSerial = $this->itemSerialRepository->findOneBy(['serial' => "test8"]);

        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.relocation.item.pick'),
            [
                'storageBin' => $itemSerial->getWarehouseStorageBin()->getSerial(),
                'itemSerial' => $itemSerial->getSerial(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertTrue($response['succeed']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'serial',
            'status',
            'inventory',
            'warehouseStorageBin',
        ], $item);

        self::assertArrayHasKeys([
            'id',
            'color',
            'guarantee',
            'size',
            'product'
        ], $item['inventory']);

        self::assertArrayHasKeys([
            'id',
            'title',
            'length',
            'width',
            'height',
            'weight',
            'mainImage'
        ], $item['inventory']['product']);

        self::assertArrayHasKeys([
            'id',
            'serial',
            'type',
        ], $item['warehouseStorageBin']);
    }

    public function testStowItemSuccess(): void
    {
        /** @var ItemSerial $itemSerial */
        $itemSerial = $this->itemSerialRepository->findOneBy(['serial' => "test7"]);

        $storageBin = $this->storageBinRepository->findOneBy([
            'type'           => StorageBinTypeDictionary::CELL,
            'heightCapacity' => 100,
        ]);

        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.relocation.item.stow'),
            [
                'storageBin' => $storageBin->getSerial(),
                'itemSerial' => $itemSerial->getSerial(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'serial',
            'status',
            'inventory',
            'warehouseStorageBin',
        ], $item);

        self::assertArrayHasKeys([
            'id',
            'color',
            'guarantee',
            'size',
            'product'
        ], $item['inventory']);

        self::assertArrayHasKeys([
            'id',
            'title',
            'length',
            'width',
            'height',
            'weight',
            'mainImage'
        ], $item['inventory']['product']);

        self::assertArrayHasKeys([
            'id',
            'serial',
            'type',
        ], $item['warehouseStorageBin']);
    }

    public function testPickBinSuccess(): void
    {
        /** @var WarehouseStorageBin $storageBin */
        $storageBin = $this->storageBinRepository->findOneBy([
            'type' => StorageBinTypeDictionary::CELL,
            'heightCapacity' => 200,
        ]);

        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.relocation.bin.pick'),
            [
                'sourceStorageBin' => $storageBin->getSerial(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertTrue($response['succeed']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'serial',
            'type',
            'isActiveForStow',
            'isActiveForPick',
        ], $item);
    }

    public function testStowBinSuccess(): void
    {
        /** @var WarehouseStorageBin $sourceStorageBin */
        $sourceStorageBin = $this->storageBinRepository->findOneBy([
            'type' => StorageBinTypeDictionary::CELL,
            'heightCapacity' => 200,
        ]);

        /** @var WarehouseStorageBin $destinationBin */
        $destinationBin = $this->storageBinRepository->findOneBy([
            'type' => StorageBinTypeDictionary::CELL,
            'heightCapacity' => 100,
        ]);

        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.relocation.bin.stow'),
            [
                'sourceStorageBin' => $sourceStorageBin->getSerial(),
                'destinationStorageBin' => $destinationBin->getSerial()
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertTrue($response['succeed']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'serial',
            'type',
            'isActiveForStow',
            'isActiveForPick',
        ], $item);
    }
}
