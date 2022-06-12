<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\StorageBinAutoGenerationActionTypeDictionary;
use App\Dictionary\StorageBinAutoGenerationStorageLevelDictionary;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea;
use App\Tests\Functional\FunctionalTestCase;

class BinAutoGenerateControllerTest extends FunctionalTestCase
{
    public function testItFailWithWrongValues(): void
    {
        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.bin.auto.generate'), [
                 'startValue'           => 'AA-A0-0A',
                 'endValue'             => 'AA-A1-0A',
                 'increment'            => '0A-01-01',
                 'storageLevel'         => 'test',
                 'actionType'           => 'test',
                 'isActiveForStow'      => 1,
                 'isActiveForPick'      => 0,
                 'quantityCapacity'     => 12,
                 'widthCapacity'        => 13,
                 'heightCapacity'       => 14,
                 'lengthCapacity'       => 15,
                 'weightCapacity'       => 13,
                 'warehouse'            => 9876543,
                 'warehouseStorageArea' => 9876543,
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $results = $response['results'];

        self::assertArrayHasKeys([
            'startValue',
            'endValue',
            'increment',
            'actionType',
            'storageLevel',
            'warehouse',
            'warehouseStorageArea',
        ], $results);

        self::assertIsArray($results['startValue']);
        self::assertIsString($results['startValue'][0]);
        self::assertEquals('The value is not compatible with template', $results['startValue'][0]);

        self::assertIsArray($results['endValue']);
        self::assertIsString($results['endValue'][0]);
        self::assertEquals('The value is not compatible with template', $results['endValue'][0]);

        self::assertIsArray($results['increment']);
        self::assertIsString($results['increment'][0]);
        self::assertEquals('Increment sections must be numeric and bigger than zero', $results['increment'][0]);

        self::assertIsArray($results['actionType']);
        self::assertIsString($results['actionType'][0]);
        self::assertEquals('The selected choice is invalid.', $results['actionType'][0]);

        self::assertIsArray($results['storageLevel']);
        self::assertIsString($results['storageLevel'][0]);
        self::assertEquals('The selected choice is invalid.', $results['storageLevel'][0]);

        self::assertIsArray($results['warehouse']);
        self::assertIsString($results['warehouse'][0]);
        self::assertEquals('The selected choice is invalid.', $results['warehouse'][0]);

        self::assertIsArray($results['warehouseStorageArea']);
        self::assertIsString($results['warehouseStorageArea'][0]);
        self::assertEquals('The selected choice is invalid.', $results['warehouseStorageArea'][0]);
    }

    public function testItCanHandleAdd(): void
    {
        $warehouse            = $this->manager->getRepository(Warehouse::class)->findOneBy([]);
        $warehouseStorageArea = $this->manager->getRepository(WarehouseStorageArea::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.bin.auto.generate'), [
                 'startValue'           => 'AA-A0-00',
                 'endValue'             => 'AB-A1-01',
                 'increment'            => '01-01-01',
                 'storageLevel'         => StorageBinAutoGenerationStorageLevelDictionary::CELL,
                 'actionType'           => StorageBinAutoGenerationActionTypeDictionary::ADD,
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

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $results = $response['results'];
        self::assertIsArray($results);
        self::assertCount(11, $results);

        $item = $results[0];

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
        self::assertIsArray($item['warehouse']);
        self::assertArrayHasKeys([
            'id',
            'title',
        ], $item['warehouse']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsArray($item['warehouseStorageArea']);
        self::assertArrayHasKeys([
            'id',
            'title',
        ], $item['warehouseStorageArea']);
        self::assertIsInt($item['warehouseStorageArea']['id']);
        self::assertIsString($item['warehouseStorageArea']['title']);
    }

    public function testItCanHandleEdit(): void
    {
        $warehouse            = $this->manager->getRepository(Warehouse::class)->findOneBy([]);
        $warehouseStorageArea = $this->manager->getRepository(WarehouseStorageArea::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.bin.auto.generate'), [
                 'startValue'           => 'AA-A0-00',
                 'endValue'             => 'AB-A1-01',
                 'increment'            => '01-01-01',
                 'storageLevel'         => StorageBinAutoGenerationStorageLevelDictionary::AISLE,
                 'actionType'           => StorageBinAutoGenerationActionTypeDictionary::EDIT,
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

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $results = $response['results'];
        self::assertIsArray($results);
        self::assertCount(3, $results);

        $item = $results[0];

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
        self::assertIsArray($item['warehouse']);
        self::assertArrayHasKeys([
            'id',
            'title',
        ], $item['warehouse']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsArray($item['warehouseStorageArea']);
        self::assertArrayHasKeys([
            'id',
            'title',
        ], $item['warehouseStorageArea']);
        self::assertIsInt($item['warehouseStorageArea']['id']);
        self::assertIsString($item['warehouseStorageArea']['title']);
    }
}
