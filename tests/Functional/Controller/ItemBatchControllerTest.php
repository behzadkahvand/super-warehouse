<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Inventory;
use App\Entity\ItemBatch;
use App\Entity\Receipt\STInboundReceipt;
use App\Tests\Functional\FunctionalTestCase;

final class ItemBatchControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)
             ->sendRequest('GET', $this->route('admin.itemBatch.index'));

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'quantity',
            'expireAt',
            'supplierBarcode',
            'consumerPrice',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsInt($item['quantity']);
        self::assertNullableDatetime($item['expireAt']);
        self::assertIsString($item['supplierBarcode']);
        self::assertIsInt($item['consumerPrice']);
    }

    public function testShow(): void
    {
        $itemBatch = $this->manager->getRepository(ItemBatch::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('GET', $this->route(
                 'admin.itemBatch.show',
                 ['id' => $itemBatch->getId()]
             ));

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'quantity',
            'expireAt',
            'supplierBarcode',
            'consumerPrice',
            'inventory',
            'receipt',
            'receiptItemBatches',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsInt($item['quantity']);
        self::assertNullableDatetime($item['expireAt']);
        self::assertIsString($item['supplierBarcode']);
        self::assertIsInt($item['consumerPrice']);

        self::assertArrayHasKey('id', $item['inventory']);
        self::assertArrayHasKey('id', $item['receipt']);

        if (count($item['receiptItemBatches'])) {
            self::assertArrayHasKey('id', $item['receiptItemBatches'][0]);
            self::assertIsInt($item['receiptItemBatches'][0]['id']);
        }
    }

    public function testStore(): void
    {
        $inventory   = $this->manager->getRepository(Inventory::class)->findOneBy([]);
        $receipt     = $this->manager->getRepository(STInboundReceipt::class)->findOneBy([]);
        $receiptItem = $receipt->getReceiptItems()->first();

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.itemBatch.store'), [
                 'quantity'        => 1,
                 'expireAt'        => date('Y-m-d', strtotime('tomorrow')),
                 'supplierBarcode' => '1234567890',
                 'consumerPrice'   => 100000,
                 'inventory'       => $inventory->getId(),
                 'receipt'         => $receipt->getId(),
                 'receiptItem'     => $receiptItem->getId(),
             ]);

        self::assertResponseStatusCodeSame(201);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'quantity',
            'expireAt',
            'supplierBarcode',
            'consumerPrice',
            'inventory',
            'receipt',
            'receiptItemBatches',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsInt($item['quantity']);
        self::assertNullableDatetime($item['expireAt']);
        self::assertIsString($item['supplierBarcode']);
        self::assertIsNumeric($item['consumerPrice']);

        self::assertArrayHasKey('id', $item['inventory']);
        self::assertArrayHasKey('id', $item['receipt']);

        if (count($item['receiptItemBatches'])) {
            self::assertArrayHasKey('id', $item['receiptItemBatches'][0]);
            self::assertIsInt($item['receiptItemBatches'][0]['id']);
        }
    }

    public function testStoreValidationFail(): void
    {
        $inventory   = $this->manager->getRepository(Inventory::class)->findOneBy([]);
        $receipt     = $this->manager->getRepository(STInboundReceipt::class)->findOneBy([]);
        $receiptItem = $receipt->getReceiptItems()->first();

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.itemBatch.store'), [
                 'quantity'        => 100,
                 'expireAt'        => date('Y-m-d', strtotime('tomorrow')),
                 'supplierBarcode' => '1234567890',
                 'consumerPrice'   => 100000,
                 'inventory'       => $inventory->getId(),
                 'receipt'         => $receipt->getId(),
                 'receiptItem'     => $receiptItem->getId(),
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKey('quantity', $item);

        self::assertIsString($item['quantity'][0]);
        self::assertEquals('The value should be smaller than receipt item quantity!', $item['quantity'][0]);
    }

    public function testShowSerials(): void
    {
        $itemBatch = $this->manager->getRepository(ItemBatch::class)->findOneBy([]);
        $this->loginAs($this->admin)
             ->sendRequest('GET', $this->route(
                 'admin.itemBatch.show.serials',
                 ['id' => $itemBatch->getId()]
             ));

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'serial',
            'status',
            'inventory',
            'warehouse',
            'warehouseStorageBin',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['serial']);
        self::assertIsString($item['status']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['warehouse']);
        self::assertIsArray($item['warehouseStorageBin']);

        self::assertArrayHasKeys(['id', 'color', 'guarantee', 'size'], $item['inventory']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertArrayHasKeys(['id', 'serial'], $item['warehouseStorageBin']);

        self::assertIsInt($item['inventory']['id']);
        self::assertIsString($item['inventory']['color']);
        self::assertIsString($item['inventory']['guarantee']);
        self::assertIsString($item['inventory']['size']);

        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);

        self::assertIsInt($item['warehouseStorageBin']['id']);
        self::assertIsString($item['warehouseStorageBin']['serial']);
    }
}
