<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Inventory;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\ReceiptItemBatch;
use App\Entity\ReceiptItemSerial;
use App\Tests\Functional\FunctionalTestCase;

final class ReceiptItemControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)
             ->sendRequest('GET', '/admin/receipt-items');

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys(['id', 'quantity', 'status'], $item);
    }

    public function testShow(): void
    {
        $receiptItem = $this->manager->getRepository(ReceiptItem::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('GET', "/admin/receipt-items/{$receiptItem->getId()}");

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'quantity',
            'status',
            'receipt',
            'inventory',
            'receiptItemBatches',
            'receiptItemSerials',
        ], $item);

        self::assertArrayHasKeys(['id', 'type'], $item['receipt']);
        self::assertArrayHasKey('id', $item['inventory']);
        self::assertArrayHasKey('id', $item['receiptItemBatches'][0]);
        self::assertArrayHasKey('id', $item['receiptItemSerials'][0]);

        self::assertIsNumeric($item['id']);
        self::assertIsNumeric($item['quantity']);
        self::assertIsString($item['status']);

        self::assertIsNumeric($item['receipt']['id']);
        self::assertIsString($item['receipt']['type']);

        self::assertIsNumeric($item['inventory']['id']);
        self::assertIsNumeric($item['receiptItemBatches'][0]['id']);
        self::assertIsNumeric($item['receiptItemSerials'][0]['id']);
    }

    public function testStore(): void
    {
        $receipt   = $this->manager->getRepository(Receipt::class)->findOneBy([]);
        $inventory = $this->manager->getRepository(Inventory::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', '/admin/receipt-items', [
                 'quantity'    => 1,
                 'receipt'     => $receipt->getId(),
                 'inventory'   => $inventory->getId(),
                 'receiptType' => ReceiptTypeDictionary::STOCK_TRANSFER,
             ]);

        self::assertResponseStatusCodeSame(201);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'quantity',
            'status',
            'receipt',
            'inventory',
            'receiptItemBatches',
            'receiptItemSerials',
        ], $item);

        self::assertArrayHasKeys(['id', 'type'], $item['receipt']);
        self::assertArrayHasKey('id', $item['inventory']);

        self::assertIsNumeric($item['id']);
        self::assertIsNumeric($item['quantity']);
        self::assertIsString($item['status']);

        self::assertIsNumeric($item['receipt']['id']);
        self::assertIsString($item['receipt']['type']);

        self::assertIsNumeric($item['inventory']['id']);
    }

    public function testUpdateSuccessfulWithDraftStatus(): void
    {
        $receipt          = $this->manager->getRepository(Receipt::class)->findOneBy(
            ['status' => 'DRAFT']
        );
        $receiptItem      = $this->manager->getRepository(ReceiptItem::class)->findOneBy([
            'status'  => 'DRAFT',
            'receipt' => $receipt,
        ]);
        $modifiedQuantity = 5;

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/receipt-items/{$receiptItem->getId()}", [
                 "quantity"    => $modifiedQuantity,
                 'receipt'     => $receipt->getId(),
                 'receiptType' => ReceiptTypeDictionary::STOCK_TRANSFER,
             ]);

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'quantity',
            'status',
            'receipt',
            'inventory',
            'receiptItemBatches',
            'receiptItemSerials',
        ], $item);

        self::assertEquals($modifiedQuantity, $item['quantity']);
        self::assertArrayHasKeys(['id', 'type'], $item['receipt']);
        self::assertArrayHasKey('id', $item['inventory']);

        self::assertIsNumeric($item['id']);
        self::assertIsNumeric($item['quantity']);
        self::assertIsString($item['status']);

        self::assertIsNumeric($item['receipt']['id']);
        self::assertIsString($item['receipt']['type']);

        self::assertIsNumeric($item['inventory']['id']);
    }

    public function testUpdateFailWithItemApprovedStatus(): void
    {
        $receiptItem      = $this->manager->getRepository(ReceiptItem::class)->findOneBy([
            'status' => ReceiptStatusDictionary::APPROVED,
        ]);
        $receipt          = $this->manager->getRepository(Receipt::class)->findOneBy([]);
        $modifiedQuantity = 5;

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/receipt-items/{$receiptItem->getId()}", [
                 "quantity"    => $modifiedQuantity,
                 'receipt'     => $receipt->getId(),
                 'receiptType' => ReceiptTypeDictionary::GOOD_ISSUE,
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertEmpty($response['results']);
        self::assertFalse($response['succeed']);
        self::assertEquals("You can only edit an item with DRAFT status!", $response['message']);
    }

    public function testUpdateFailWithReceiptApprovedStatus(): void
    {
        $receiptItem      = $this->manager->getRepository(ReceiptItem::class)->findOneBy([
            'status' => ReceiptStatusDictionary::DRAFT,
        ]);
        $receipt          = $this->manager->getRepository(Receipt::class)->findOneBy([
            'status' => ReceiptStatusDictionary::APPROVED,
        ]);
        $modifiedQuantity = 5;

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/receipt-items/{$receiptItem->getId()}", [
                 "quantity"    => $modifiedQuantity,
                 'receipt'     => $receipt->getId(),
                 'receiptType' => ReceiptTypeDictionary::GOOD_ISSUE,
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertEmpty($response['results']);
        self::assertFalse($response['succeed']);
        self::assertEquals("You can only edit an item that it's receipt has DRAFT status!", $response['message']);
    }

    public function testDeleteSuccessfully(): void
    {
        $receipt     = $this->manager->getRepository(Receipt::class)->findOneBy(
            ['status' => 'DRAFT']
        );
        $receiptItem = $this->manager->getRepository(ReceiptItem::class)->findOneBy([
            'status'  => 'DRAFT',
            'receipt' => $receipt,
        ]);
        $client      = $this->loginAs($this->admin)->sendRequest(
            'DELETE',
            "/admin/receipt-items/{$receiptItem->getId()}"
        );

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $response = $this->getControllerResponse();
        self::assertArrayHasKey('succeed', $response);
        self::assertArrayHasKey('message', $response);
        self::assertArrayHasKey('results', $response);
        self::assertArrayHasKey('metas', $response);

        $result = $response['results'];
        self::assertArrayHasKey('id', $result);
    }

    public function testDeleteFailedWithItemApprovedStatus(): void
    {
        $receiptItem = $this->manager->getRepository(ReceiptItem::class)->findOneBy([
            'status' => ReceiptStatusDictionary::APPROVED,
        ]);
        $client      = $this->loginAs($this->admin)->sendRequest(
            'DELETE',
            "/admin/receipt-items/{$receiptItem->getId()}"
        );

        self::assertEquals(422, $client->getResponse()->getStatusCode());
    }

    public function testDeleteFailedWithReceiptApprovedStatus(): void
    {
        $receipt     = $this->manager->getRepository(Receipt::class)->findOneBy([
            'status' => ReceiptStatusDictionary::APPROVED,
            'description' => 'test1'
        ]);
        $receiptItem = $this->manager->getRepository(ReceiptItem::class)->findOneBy([
            'status'  => 'DRAFT',
            'receipt' => $receipt,
        ]);
        $client      = $this->loginAs($this->admin)->sendRequest(
            'DELETE',
            "/admin/receipt-items/{$receiptItem->getId()}"
        );

        self::assertEquals(422, $client->getResponse()->getStatusCode());
    }
}
