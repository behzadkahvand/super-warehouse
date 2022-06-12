<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\Inventory;
use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageBin;
use App\Tests\Functional\FunctionalTestCase;

class ItemSerialControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.item.serial.index')
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'serial',
            'status',
            'inventory',
            'warehouse',
            'warehouseStorageBin',
        ], $item);
        self::assertArrayHasKeys(['color', 'guarantee', 'size'], $item['inventory']);
        self::assertArrayHasKey('title', $item['warehouse']);
        self::assertArrayHasKey('serial', $item['warehouseStorageBin']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['serial']);
        self::assertIsString($item['status']);
        self::assertIsArray($item['warehouseStorageBin']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['warehouse']);
        self::assertIsString($item['inventory']['color']);
        self::assertIsString($item['inventory']['guarantee']);
        self::assertIsString($item['inventory']['size']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsString($item['warehouseStorageBin']['serial']);
    }

    public function testShow(): void
    {
        $itemSerial = $this->manager->getRepository(ItemSerial::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.item.serial.show', ['id' => $itemSerial->getId()])
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
            'warehouse',
            'warehouseStorageBin',
        ], $item);
        self::assertArrayHasKeys(['color', 'guarantee', 'size'], $item['inventory']);
        self::assertArrayHasKey('title', $item['warehouse']);
        self::assertArrayHasKey('serial', $item['warehouseStorageBin']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['serial']);
        self::assertIsString($item['status']);
        self::assertIsArray($item['warehouseStorageBin']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['warehouse']);
        self::assertIsString($item['inventory']['color']);
        self::assertIsString($item['inventory']['guarantee']);
        self::assertIsString($item['inventory']['size']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsString($item['warehouseStorageBin']['serial']);
    }

    public function testAutoStore(): void
    {
        $itemBatch           = $this->manager->getRepository(ItemBatch::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.item.serial.auto.store'),
            [
                'itemBatch'           => $itemBatch->getId(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'serial',
            'status',
            'inventory',
            'warehouse',
            'warehouseStorageBin',
        ], $item);
        self::assertArrayHasKeys(['color', 'guarantee', 'size'], $item['inventory']);
        self::assertArrayHasKeys(['title', 'id'], $item['warehouse']);

        self::assertIsInt($item['id']);
        self::assertNull($item['serial']);
        self::assertIsString($item['status']);
        self::assertNull($item['warehouseStorageBin']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['warehouse']);
        self::assertIsString($item['inventory']['color']);
        self::assertIsString($item['inventory']['guarantee']);
        self::assertIsString($item['inventory']['size']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsInt($item['warehouse']['id']);
    }

    public function testCustomStore(): void
    {
        $itemBatch           = $this->manager->getRepository(ItemBatch::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.item.serial.custom.store'),
            [
                'itemBatch'           => $itemBatch->getId(),
                'serials' => ['test11']
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'serial',
            'status',
            'inventory',
            'warehouse',
            'warehouseStorageBin',
        ], $item);
        self::assertArrayHasKeys(['color', 'guarantee', 'size'], $item['inventory']);
        self::assertArrayHasKeys(['title', 'id'], $item['warehouse']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['serial']);
        self::assertIsString($item['status']);
        self::assertNull($item['warehouseStorageBin']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['warehouse']);
        self::assertIsString($item['inventory']['color']);
        self::assertIsString($item['inventory']['guarantee']);
        self::assertIsString($item['inventory']['size']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsInt($item['warehouse']['id']);
    }

    public function testUpdate(): void
    {
        $itemSerial = $this->manager->getRepository(ItemSerial::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.item.serial.update', ['id' => $itemSerial->getId()]),
            [
                'status' => ItemSerialStatusDictionary::OUT_OF_STOCK,
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
            'warehouse',
            'warehouseStorageBin',
        ], $item);

        self::assertEquals(ItemSerialStatusDictionary::OUT_OF_STOCK, $item['status']);

        self::assertArrayHasKeys(['color', 'guarantee', 'size'], $item['inventory']);
        self::assertArrayHasKey('title', $item['warehouse']);
        self::assertArrayHasKey('serial', $item['warehouseStorageBin']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['serial']);
        self::assertIsString($item['status']);
        self::assertIsArray($item['warehouseStorageBin']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['warehouse']);
        self::assertIsString($item['inventory']['color']);
        self::assertIsString($item['inventory']['guarantee']);
        self::assertIsString($item['inventory']['size']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsString($item['warehouseStorageBin']['serial']);
    }
}
