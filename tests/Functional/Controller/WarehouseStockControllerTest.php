<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\FunctionalTestCase;

class WarehouseStockControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.warehouse.stock.index')
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'physicalStock',
            'saleableStock',
            'reserveStock',
            'quarantineStock',
            'supplyStock',
            'product',
            'inventory',
            'warehouse',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsInt($item['physicalStock']);
        self::assertIsInt($item['saleableStock']);
        self::assertIsInt($item['reserveStock']);
        self::assertIsInt($item['quarantineStock']);
        self::assertIsInt($item['supplyStock']);
        self::assertIsArray($item['product']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['warehouse']);

        self::assertArrayHasKeys([
            'id',
            'title',
        ], $item['product']);

        self::assertIsInt($item['product']['id']);
        self::assertIsString($item['product']['title']);

        self::assertArrayHasKeys([
            'id',
            'color',
            'guarantee',
            'size',
        ], $item['inventory']);

        self::assertIsInt($item['inventory']['id']);
        self::assertIsString($item['inventory']['color']);
        self::assertIsString($item['inventory']['guarantee']);
        self::assertIsString($item['inventory']['size']);

        self::assertArrayHasKeys([
            'id',
            'title',
        ], $item['warehouse']);

        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
    }
}
