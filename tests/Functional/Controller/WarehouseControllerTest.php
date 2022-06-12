<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\WarehousePickingStrategyDictionary;
use App\Dictionary\WarehousePickingTypeDictionary;
use App\Dictionary\WarehouseTrackingTypeDictionary;
use App\Entity\Admin;
use App\Entity\Warehouse;
use App\Tests\Functional\FunctionalTestCase;

class WarehouseControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest('GET', $this->route('admin.warehouse.index'));

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'title',
            'isActive',
            'trackingType',
            'pickingType',
            'pickingStrategy',
            'forSale',
            'forRetailPurchase',
            'forMarketPlacePurchase',
            'forFmcgMarketPlacePurchase',
            'forSalesReturn',
            'phone',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertContains($item['trackingType'], WarehouseTrackingTypeDictionary::values());
        self::assertContains($item['pickingType'], WarehousePickingTypeDictionary::values());
        self::assertContains($item['pickingStrategy'], WarehousePickingStrategyDictionary::values());
        self::assertIsBool($item['isActive']);
        self::assertIsBool($item['forSale']);
        self::assertIsBool($item['forRetailPurchase']);
        self::assertIsBool($item['forMarketPlacePurchase']);
        self::assertIsBool($item['forFmcgMarketPlacePurchase']);
        self::assertIsBool($item['forSalesReturn']);
        self::assertIsString($item['phone']);
    }

    public function testShow(): void
    {
        $warehouse = $this->manager->getRepository(Warehouse::class)->findOneBy([]);
        $this->loginAs($this->admin)
             ->sendRequest('GET', $this->route('admin.warehouse.show', ['id' => $warehouse->getId()]));

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'title',
            'isActive',
            'trackingType',
            'pickingType',
            'pickingStrategy',
            'forSale',
            'forRetailPurchase',
            'forMarketPlacePurchase',
            'forFmcgMarketPlacePurchase',
            'forSalesReturn',
            'phone',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertContains($item['trackingType'], WarehouseTrackingTypeDictionary::values());
        self::assertContains($item['pickingType'], WarehousePickingTypeDictionary::values());
        self::assertContains($item['pickingStrategy'], WarehousePickingStrategyDictionary::values());
        self::assertIsBool($item['isActive']);
        self::assertIsBool($item['forSale']);
        self::assertIsBool($item['forRetailPurchase']);
        self::assertIsBool($item['forMarketPlacePurchase']);
        self::assertIsBool($item['forFmcgMarketPlacePurchase']);
        self::assertIsBool($item['forSalesReturn']);
        self::assertIsString($item['phone']);
    }

    public function testStore(): void
    {
        $owner = $this->manager->getRepository(Admin::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.warehouse.store'),
            [
                'title'                      => 'test',
                'isActive'                   => false,
                "trackingType"               => WarehouseTrackingTypeDictionary::SERIAL,
                "pickingType"                => WarehousePickingTypeDictionary::SHIPMENT,
                "pickingStrategy"            => WarehousePickingStrategyDictionary::FIFO,
                "forSale"                    => 1,
                "forRetailPurchase"          => 0,
                "forMarketPlacePurchase"     => 0,
                "forFmcgMarketPlacePurchase" => 1,
                "forSalesReturn"             => 0,
                "phone"                      => "02165237648",
                "coordinates"                => [
                    "lat"  => 35.43,
                    "long" => 51.56,
                ],
                "address"                    => "tehran",
                "owner"                      => $owner->getId(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'title',
            'isActive',
            'trackingType',
            'pickingType',
            'pickingStrategy',
            'forSale',
            'forRetailPurchase',
            'forMarketPlacePurchase',
            'forFmcgMarketPlacePurchase',
            'forSalesReturn',
            'phone',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertContains($item['trackingType'], WarehouseTrackingTypeDictionary::values());
        self::assertContains($item['pickingType'], WarehousePickingTypeDictionary::values());
        self::assertContains($item['pickingStrategy'], WarehousePickingStrategyDictionary::values());
        self::assertIsBool($item['isActive']);
        self::assertIsBool($item['forSale']);
        self::assertIsBool($item['forRetailPurchase']);
        self::assertIsBool($item['forMarketPlacePurchase']);
        self::assertIsBool($item['forFmcgMarketPlacePurchase']);
        self::assertIsBool($item['forSalesReturn']);
        self::assertIsString($item['phone']);
    }

    public function testStoreValidationError(): void
    {
        $owner = $this->manager->getRepository(Admin::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.warehouse.store'),
            [
                'title'                      => 'test',
                'isActive'                   => false,
                "trackingType"               => 'test',
                "pickingStrategy"            => 'test',
                "forSale"                    => 1,
                "forRetailPurchase"          => 0,
                "forMarketPlacePurchase"     => 0,
                "forFmcgMarketPlacePurchase" => 0,
                "forSalesReturn"             => 0,
                "phone"                      => "02165237648",
                "coordinates"                => [
                    "lat"  => 35.43,
                    "long" => 51.56,
                ],
                "address"                    => "tehran",
                "owner"                      => $owner->getId(),
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKey('trackingType', $item);
        self::assertEquals($item['trackingType'][0], 'The selected choice is invalid.');
    }

    public function testUpdate(): void
    {
        $warehouse = $this->manager->getRepository(Warehouse::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.warehouse.update', ['id' => $warehouse->getId()]),
            [
                'title' => 'test2',
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'title',
            'isActive',
            'trackingType',
            'pickingType',
            'pickingStrategy',
            'forSale',
            'forRetailPurchase',
            'forMarketPlacePurchase',
            'forFmcgMarketPlacePurchase',
            'forSalesReturn',
            'phone',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertContains($item['trackingType'], WarehouseTrackingTypeDictionary::values());
        self::assertContains($item['pickingType'], WarehousePickingTypeDictionary::values());
        self::assertContains($item['pickingStrategy'], WarehousePickingStrategyDictionary::values());
        self::assertIsBool($item['isActive']);
        self::assertIsBool($item['forSale']);
        self::assertIsBool($item['forRetailPurchase']);
        self::assertIsBool($item['forMarketPlacePurchase']);
        self::assertIsBool($item['forFmcgMarketPlacePurchase']);
        self::assertIsBool($item['forSalesReturn']);
        self::assertIsString($item['phone']);
    }
}
