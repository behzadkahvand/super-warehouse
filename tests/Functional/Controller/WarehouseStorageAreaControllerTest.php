<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\StorageAreaCapacityCheckMethodDictionary;
use App\Dictionary\StorageAreaStowingStrategyDictionary;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea;
use App\Tests\Functional\FunctionalTestCase;

class WarehouseStorageAreaControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest('GET', '/admin/warehouse-storage-areas');

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'title',
            'stowingStrategy',
            'capacityCheckMethod',
            'isActive',
            'warehouse',
        ], $item);

        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertContains($item['stowingStrategy'], StorageAreaStowingStrategyDictionary::values());
        self::assertContains($item['capacityCheckMethod'], StorageAreaCapacityCheckMethodDictionary::values());
        self::assertIsBool($item['isActive']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
    }

    public function testShow(): void
    {
        $warehouse = $this->manager->getRepository(WarehouseStorageArea::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('GET', "/admin/warehouse-storage-areas/{$warehouse->getId()}");

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'title',
            'stowingStrategy',
            'capacityCheckMethod',
            'isActive',
            'warehouse',
        ], $item);

        self::assertArrayHasKeys([
            'id',
            'title',
            'forSale',
            'forRetailPurchase',
            'forMarketPlacePurchase',
            'forSalesReturn',
            'phone',
            'address',
        ], $item['warehouse']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertContains($item['stowingStrategy'], StorageAreaStowingStrategyDictionary::values());
        self::assertContains($item['capacityCheckMethod'], StorageAreaCapacityCheckMethodDictionary::values());
        self::assertIsBool($item['isActive']);

        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsBool($item['warehouse']['forSale']);
        self::assertIsBool($item['warehouse']['forRetailPurchase']);
        self::assertIsBool($item['warehouse']['forMarketPlacePurchase']);
        self::assertIsBool($item['warehouse']['forSalesReturn']);
        self::assertIsString($item['warehouse']['phone']);
        self::assertIsString($item['warehouse']['address']);
    }

    public function testStore(): void
    {
        $warehouse = $this->manager->getRepository(Warehouse::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', '/admin/warehouse-storage-areas', [
                 'title'                => 'warehouse storage area title',
                 'isActive'             => true,
                 'warehouse'            => $warehouse->getId(),
                 'stowingStrategy'      => 'NONE',
                 'capacityCheckMethod'  => 'NONE',
             ]);

        self::assertResponseStatusCodeSame(201);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'title',
            'stowingStrategy',
            'capacityCheckMethod',
            'isActive',
            'warehouse',
        ], $item);

        self::assertArrayHasKeys([
            'id',
            'title',
            'forSale',
            'forRetailPurchase',
            'forMarketPlacePurchase',
            'forSalesReturn',
            'phone',
            'address',
        ], $item['warehouse']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertContains($item['stowingStrategy'], StorageAreaStowingStrategyDictionary::values());
        self::assertContains($item['capacityCheckMethod'], StorageAreaCapacityCheckMethodDictionary::values());
        self::assertIsBool($item['isActive']);

        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsBool($item['warehouse']['forSale']);
        self::assertIsBool($item['warehouse']['forRetailPurchase']);
        self::assertIsBool($item['warehouse']['forMarketPlacePurchase']);
        self::assertIsBool($item['warehouse']['forSalesReturn']);
        self::assertIsString($item['warehouse']['phone']);
        self::assertIsString($item['warehouse']['address']);
    }

    public function testUpdate(): void
    {
        $warehouseStorageArea = $this->manager->getRepository(WarehouseStorageArea::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/warehouse-storage-areas/{$warehouseStorageArea->getId()}", [
                 'title' => 'warehouseStorageArea storage area title modified!',
             ]);

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'title',
            'stowingStrategy',
            'capacityCheckMethod',
            'isActive',
            'warehouse',
        ], $item);

        self::assertArrayHasKeys([
            'id',
            'title',
            'forSale',
            'forRetailPurchase',
            'forMarketPlacePurchase',
            'forSalesReturn',
            'phone',
            'address',
        ], $item['warehouse']);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertContains($item['stowingStrategy'], StorageAreaStowingStrategyDictionary::values());
        self::assertContains($item['capacityCheckMethod'], StorageAreaCapacityCheckMethodDictionary::values());
        self::assertIsBool($item['isActive']);

        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsBool($item['warehouse']['forSale']);
        self::assertIsBool($item['warehouse']['forRetailPurchase']);
        self::assertIsBool($item['warehouse']['forMarketPlacePurchase']);
        self::assertIsBool($item['warehouse']['forSalesReturn']);
        self::assertIsString($item['warehouse']['phone']);
        self::assertIsString($item['warehouse']['address']);
    }
}
