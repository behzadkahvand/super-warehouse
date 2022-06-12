<?php

namespace App\Tests\Functional\Controller;

use App\Entity\SellerPackage;
use App\Entity\SellerPackageItem;
use App\Tests\Functional\FunctionalTestCase;

class SellerPackageItemControllerTest extends FunctionalTestCase
{
    public function testShowItems(): void
    {
        /** @var SellerPackage $sellerPackage */
        $sellerPackage = $this->manager->getRepository(SellerPackage::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.seller.package.item.show', [
                'sellerPackageId' => $sellerPackage->getId(),
            ])
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'status',
            'expectedQuantity',
            'actualQuantity',
            'inventory',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['status']);
        self::assertIsInt($item['expectedQuantity']);
        self::assertIsInt($item['actualQuantity']);
        self::assertIsArray($item['inventory']);

        self::assertArrayHasKeys([
            'id',
            'color',
            'size',
            'guarantee',
            'product',
        ], $item['inventory']);

        self::assertIsInt($item['inventory']['id']);
        self::assertIsString($item['inventory']['size']);
        self::assertIsString($item['inventory']['color']);
        self::assertIsString($item['inventory']['guarantee']);
        self::assertIsArray($item['inventory']['product']);

        self::assertArrayHasKeys([
            'id',
            'title',
        ], $item['inventory']['product']);

        self::assertIsInt($item['inventory']['product']['id']);
        self::assertIsString($item['inventory']['product']['title']);
    }

    public function testUpdateValidationFail(): void
    {
        /** @var SellerPackage $sellerPackage */
        $sellerPackage = $this->manager->getRepository(SellerPackage::class)->findOneBy([]);
        /** @var SellerPackageItem $item */
        $item = $sellerPackage->getPackageItems()->first();
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.seller.package.item.update', [
                'sellerPackageId' => $sellerPackage->getId(),
                'id'              => $item->getId(),
            ]),
            [
                'actualQuantity' => $item->getExpectedQuantity() + 1,
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);
        self::assertArrayHasKey('actualQuantity', $response['results']);
        self::assertIsArray($response['results']['actualQuantity']);
        self::assertEquals('The value should be smaller than expectedQuantity!', $response['results']['actualQuantity'][0]);
    }

    public function testUpdate(): void
    {
        /** @var SellerPackage $sellerPackage */
        $sellerPackage = $this->manager->getRepository(SellerPackage::class)->findOneBy([]);
        /** @var SellerPackageItem $item */
        $item = $sellerPackage->getPackageItems()->first();
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.seller.package.item.update', [
                'sellerPackageId' => $sellerPackage->getId(),
                'id'              => $item->getId(),
            ]),
            [
                'actualQuantity' => 0,
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
            'expectedQuantity',
            'actualQuantity',
            'inventory',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['status']);
        self::assertIsInt($item['expectedQuantity']);
        self::assertIsInt($item['actualQuantity']);
        self::assertEquals(0, $item['actualQuantity']);
        self::assertIsArray($item['inventory']);

        self::assertArrayHasKeys([
            'id',
            'color',
            'size',
            'guarantee',
            'product',
        ], $item['inventory']);

        self::assertIsInt($item['inventory']['id']);
        self::assertIsString($item['inventory']['size']);
        self::assertIsString($item['inventory']['color']);
        self::assertIsString($item['inventory']['guarantee']);
        self::assertIsArray($item['inventory']['product']);

        self::assertArrayHasKeys([
            'id',
            'title',
        ], $item['inventory']['product']);

        self::assertIsInt($item['inventory']['product']['id']);
        self::assertIsString($item['inventory']['product']['title']);
    }
}
