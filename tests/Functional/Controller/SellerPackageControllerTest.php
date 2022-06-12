<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\SellerPackageStatusDictionary;
use App\Entity\SellerPackage;
use App\Entity\Warehouse;
use App\Tests\Functional\FunctionalTestCase;

class SellerPackageControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.seller.package.index')
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'status',
            'createdAt',
            'seller',
            'warehouse',
            'totalQuantity',
            'inventoryCount',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['status']);
        self::assertIsString($item['createdAt']);
        self::assertIsArray($item['seller']);
        self::assertIsArray($item['warehouse']);
        self::assertIsInt($item['totalQuantity']);
        self::assertIsInt($item['inventoryCount']);
        self::assertArrayHasKey('name', $item['seller']);
        self::assertIsString($item['seller']['name']);
        self::assertArrayHasKey('title', $item['warehouse']);
        self::assertIsString($item['warehouse']['title']);
    }

    public function testUpdate(): void
    {
        $warehouse     = $this->manager->getRepository(Warehouse::class)->findOneBy([]);
        $sellerPackage = $this->manager->getRepository(SellerPackage::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.seller.package.update', ['id' => $sellerPackage->getId()]),
            [
                'warehouse' => $warehouse->getId(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'status',
            'createdAt',
            'seller',
            'warehouse',
            'totalQuantity',
            'inventoryCount',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['status']);
        self::assertIsString($item['createdAt']);
        self::assertIsArray($item['seller']);
        self::assertIsArray($item['warehouse']);
        self::assertIsInt($item['totalQuantity']);
        self::assertIsInt($item['inventoryCount']);
        self::assertArrayHasKey('name', $item['seller']);
        self::assertIsString($item['seller']['name']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
    }

    public function testCancel(): void
    {
        $sellerPackage = $this->manager->getRepository(SellerPackage::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.seller.package.cancel', ['id' => $sellerPackage->getId()])
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'status',
            'createdAt',
            'seller',
            'warehouse',
            'totalQuantity',
            'inventoryCount',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['status']);
        self::assertEquals(SellerPackageStatusDictionary::CANCELED, $item['status']);
        self::assertIsString($item['createdAt']);
        self::assertIsArray($item['seller']);
        self::assertIsArray($item['warehouse']);
        self::assertIsInt($item['totalQuantity']);
        self::assertIsInt($item['inventoryCount']);
        self::assertArrayHasKey('name', $item['seller']);
        self::assertIsString($item['seller']['name']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
    }
}
