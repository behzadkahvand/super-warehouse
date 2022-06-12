<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\StorageBinTypeDictionary;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Tests\Functional\FunctionalTestCase;

class WarehouseStorageBinControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest('GET', $this->route('admin.warehouse.storage.bin.index'));

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'type',
            'serial',
            'isActiveForStow',
            'isActiveForPick',
            'quantityCapacity',
            'widthCapacity',
            'heightCapacity',
            'lengthCapacity',
            'weightCapacity',
            'warehouse',
            'warehouseStorageArea',
        ], $item);

        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouseStorageArea']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['type']);
        self::assertIsString($item['serial']);
        self::assertIsBool($item['isActiveForStow']);
        self::assertIsBool($item['isActiveForPick']);
        self::assertIsInt($item['quantityCapacity']);
        self::assertIsInt($item['widthCapacity']);
        self::assertIsInt($item['heightCapacity']);
        self::assertIsInt($item['lengthCapacity']);
        self::assertIsInt($item['weightCapacity']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsInt($item['warehouseStorageArea']['id']);
        self::assertIsString($item['warehouseStorageArea']['title']);
    }

    public function testShow(): void
    {
        $warehouseStorageBin = $this->manager->getRepository(WarehouseStorageBin::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('GET', $this->route(
                 'admin.warehouse.storage.bin.show',
                 ['id' => $warehouseStorageBin->getId()]
             ));

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'type',
            'serial',
            'isActiveForStow',
            'isActiveForPick',
            'quantityCapacity',
            'widthCapacity',
            'heightCapacity',
            'lengthCapacity',
            'weightCapacity',
            'warehouse',
            'warehouseStorageArea',
        ], $item);

        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouseStorageArea']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['type']);
        self::assertIsString($item['serial']);
        self::assertIsBool($item['isActiveForStow']);
        self::assertIsBool($item['isActiveForPick']);
        self::assertIsInt($item['quantityCapacity']);
        self::assertIsInt($item['widthCapacity']);
        self::assertIsInt($item['heightCapacity']);
        self::assertIsInt($item['lengthCapacity']);
        self::assertIsInt($item['weightCapacity']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsInt($item['warehouseStorageArea']['id']);
        self::assertIsString($item['warehouseStorageArea']['title']);
    }

    public function testStore(): void
    {
        $warehouse            = $this->manager->getRepository(Warehouse::class)->findOneBy([]);
        $warehouseStorageArea = $this->manager->getRepository(WarehouseStorageArea::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.warehouse.storage.bin.store'), [
                 'type'                 => StorageBinTypeDictionary::AISLE,
                 'serial'               => 'test',
                 'isActiveForStow'      => 1,
                 'isActiveForPick'      => 0,
                 'quantityCapacity'     => 12,
                 'widthCapacity'        => 13,
                 'heightCapacity'       => 14,
                 'lengthCapacity'       => 15,
                 'weightCapacity'       => 13,
                 'warehouse'            => $warehouse->getId(),
                 'warehouseStorageArea' => $warehouseStorageArea->getId(),
             ]);

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'type',
            'serial',
            'isActiveForStow',
            'isActiveForPick',
            'quantityCapacity',
            'widthCapacity',
            'heightCapacity',
            'lengthCapacity',
            'weightCapacity',
            'warehouse',
            'warehouseStorageArea',
        ], $item);

        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouseStorageArea']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['type']);
        self::assertIsString($item['serial']);
        self::assertIsBool($item['isActiveForStow']);
        self::assertIsBool($item['isActiveForPick']);
        self::assertIsInt($item['quantityCapacity']);
        self::assertIsInt($item['widthCapacity']);
        self::assertIsInt($item['heightCapacity']);
        self::assertIsInt($item['lengthCapacity']);
        self::assertIsInt($item['weightCapacity']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsInt($item['warehouseStorageArea']['id']);
        self::assertIsString($item['warehouseStorageArea']['title']);
    }

    public function testStoreValidationError(): void
    {
        $warehouse            = $this->manager->getRepository(Warehouse::class)->findOneBy([]);
        $warehouseStorageArea = $this->manager->getRepository(WarehouseStorageArea::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.warehouse.storage.bin.store'), [
                 'type'                 => 'test',
                 'serial'               => 'test',
                 'isActiveForStow'      => 1,
                 'isActiveForPick'      => 0,
                 'quantityCapacity'     => 12,
                 'widthCapacity'        => 13,
                 'heightCapacity'       => 14,
                 'lengthCapacity'       => 15,
                 'weightCapacity'       => 13,
                 'warehouse'            => $warehouse->getId(),
                 'warehouseStorageArea' => $warehouseStorageArea->getId(),
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKey('type', $item);
        self::assertEquals('The selected choice is invalid.', $item['type'][0]);
    }

    public function testUpdate(): void
    {
        $warehouseStorageBin = $this->manager->getRepository(WarehouseStorageBin::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest(
                 'PATCH',
                 $this->route('admin.warehouse.storage.bin.update', ['id' => $warehouseStorageBin->getId()]),
                 [
                     'type' => StorageBinTypeDictionary::BAY,
                 ]
             );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'type',
            'serial',
            'isActiveForStow',
            'isActiveForPick',
            'quantityCapacity',
            'widthCapacity',
            'heightCapacity',
            'lengthCapacity',
            'weightCapacity',
            'warehouse',
            'warehouseStorageArea',
        ], $item);

        self::assertEquals(StorageBinTypeDictionary::BAY, $item['type']);

        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouseStorageArea']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['type']);
        self::assertIsString($item['serial']);
        self::assertIsBool($item['isActiveForStow']);
        self::assertIsBool($item['isActiveForPick']);
        self::assertIsInt($item['quantityCapacity']);
        self::assertIsInt($item['widthCapacity']);
        self::assertIsInt($item['heightCapacity']);
        self::assertIsInt($item['lengthCapacity']);
        self::assertIsInt($item['weightCapacity']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsInt($item['warehouseStorageArea']['id']);
        self::assertIsString($item['warehouseStorageArea']['title']);
    }
}
