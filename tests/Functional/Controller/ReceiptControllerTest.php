<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\Dictionary\SellerPackageStatusDictionary;
use App\Entity\Receipt;
use App\Entity\Receipt\GINoneReceipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\Receipt\STInboundReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use App\Entity\ReceiptItem;
use App\Entity\SellerPackage;
use App\Entity\Warehouse;
use App\Tests\Functional\FunctionalTestCase;

final class ReceiptControllerTest extends FunctionalTestCase
{
    public function testItCanGetReceiptListWithoutAnyFilters(): void
    {
        $this->loginAs($this->admin)
             ->sendRequest(
                 'GET',
                 $this->route('admin.receipt.index'),
             );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys(
            [
                'id',
                'type',
                'referenceType',
                'status',
                'sourceWarehouse',
                'receiptItems',
                'destinationWarehouse',
                'referenceId',
            ],
            $item
        );

        self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        if ($item['receiptItems']) {
            self::assertArrayHasKeys(['id',
                'quantity',
                'status',
                'inventory',
                'receiptItemBatches',
                'receiptItemSerials'], $item['receiptItems'][0]);
            self::assertArrayHasKeys(['id', 'product'], $item['receiptItems'][0]['inventory']);
            self::assertArrayHasKeys(['id', 'title'], $item['receiptItems'][0]['inventory']['product']);
            if ($item['receiptItems'][0]['receiptItemSerials']) {
                self::assertArrayHasKeys(['id', 'itemSerial'], $item['receiptItems'][0]['receiptItemSerials']);
                self::assertArrayHasKeys(
                    ['id', 'serial', 'status'],
                    $item['receiptItems'][0]['receiptItemSerials']['itemSerial']
                );
            }
            if ($item['receiptItems'][0]['receiptItemBatches']) {
                self::assertArrayHasKeys(['id', 'itemBatch'], $item['receiptItems'][0]['receiptItemSerials']);
                self::assertArrayHasKeys(
                    ['id', 'quantity', 'expireAt', 'supplierBarcode', 'consumerPrice'],
                    $item['receiptItems'][0]['receiptItemSerials']['itemBatch']
                );
            }
        }
    }

    public function testItCanGetReceiptListWithFilterReferenceId(): void
    {
        $sellePackage = $this->manager->getRepository(SellerPackage::class)->findOneBy([]);
        $this->loginAs($this->admin)
             ->sendRequest(
                 'GET',
                 $this->route('admin.receipt.index'),
                 [
                     'filter' => [
                         'reference.id' => $sellePackage->getId(),
                         'type'         => ReceiptTypeDictionary::GOOD_RECEIPT,
                     ],
                 ]
             );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys(
            [
                'id',
                'type',
                'referenceType',
                'status',
                'sourceWarehouse',
                'receiptItems',
                'destinationWarehouse',
                'referenceId',
            ],
            $item
        );

        self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        if ($item['receiptItems']) {
            self::assertArrayHasKeys(['id',
                'quantity',
                'status',
                'inventory',
                'receiptItemBatches',
                'receiptItemSerials'], $item['receiptItems'][0]);
            self::assertArrayHasKeys(['id', 'product'], $item['receiptItems'][0]['inventory']);
            self::assertArrayHasKeys(['id', 'title'], $item['receiptItems'][0]['inventory']['product']);
            if ($item['receiptItems'][0]['receiptItemSerials']) {
                self::assertArrayHasKeys(['id', 'itemSerial'], $item['receiptItems'][0]['receiptItemSerials']);
                self::assertArrayHasKeys(
                    ['id', 'serial', 'status'],
                    $item['receiptItems'][0]['receiptItemSerials']['itemSerial']
                );
            }
            if ($item['receiptItems'][0]['receiptItemBatches']) {
                self::assertArrayHasKeys(['id', 'itemBatch'], $item['receiptItems'][0]['receiptItemSerials']);
                self::assertArrayHasKeys(
                    ['id', 'quantity', 'expireAt', 'supplierBarcode', 'consumerPrice'],
                    $item['receiptItems'][0]['receiptItemSerials']['itemBatch']
                );
            }
        }
    }

    public function testShow(): void
    {
        $receipt = $this->manager->getRepository(STInboundReceipt::class)->findOneBy([]);

        $this->loginAs($this->admin)
             ->sendRequest('GET', "/admin/receipts/{$receipt->getId()}");

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'type',
            'referenceType',
            'description',
            'status',
            'sourceWarehouse',
            'destinationWarehouse',
            'referenceId',
            'inboundReceipt',
            'receiptItems',
            'itemBatches',
        ], $item);

        self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        self::assertArrayHasKeys(['id', 'quantity', 'status', 'inventory'], $item['receiptItems'][0]);
        self::assertArrayHasKey('id', $item['receiptItems'][0]['inventory']);
        self::assertArrayHasKeys(['id', 'quantity'], $item['itemBatches'][0]);

        self::assertIsNumeric($item['id']);
        self::assertIsString($item['type']);
        self::assertNullableString($item['referenceType']);
        self::assertNullableString($item['description']);
        self::assertIsString($item['status']);

        self::assertNullableArray($item['sourceWarehouse']);
        if (isset($item['sourceWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        }

        self::assertNullableArray($item['destinationWarehouse']);
        if (isset($item['destinationWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['destinationWarehouse']);
        }

        self::assertNullableArray($item['inboundReceipt']);

        self::assertIsArray($item['receiptItems']);
        if (count($item['receiptItems'])) {
            self::assertArrayHasKeys(['id', 'quantity', 'status', 'inventory'], $item['receiptItems'][0]);
            self::assertArrayHasKeys(['id'], $item['receiptItems'][0]['inventory']);
        }

        self::assertIsArray($item['itemBatches']);
        if (count($item['itemBatches'])) {
            self::assertArrayHasKeys(['id', 'quantity'], $item['itemBatches'][0]);
        }
    }

    public function testStoreGoodReceipt(): void
    {
        $warehouse    = $this->manager->getRepository(Warehouse::class)->findOneBy([]);
        $sellePackage = $this->manager->getRepository(SellerPackage::class)
                                      ->findOneBy([
                                          'status' => SellerPackageStatusDictionary::SENT,
                                      ]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.receipt.store.good.receipt'), [
                 "sellerPackage" => $sellePackage->getId(),
                 "warehouse"     => $warehouse->getId(),
             ]);

        self::assertResponseStatusCodeSame(201);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'type',
            'referenceType',
            'description',
            'status',
            'sourceWarehouse',
            'destinationWarehouse',
            'referenceId',
            'inboundReceipt',
            'receiptItems',
        ], $item);

        self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);

        self::assertIsNumeric($item['id']);
        self::assertIsString($item['type']);
        self::assertNullableString($item['referenceType']);
        self::assertNullableString($item['description']);
        self::assertIsString($item['status']);

        self::assertNullableArray($item['sourceWarehouse']);
        if (isset($item['sourceWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        }

        self::assertNullableArray($item['destinationWarehouse']);
        if (isset($item['destinationWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['destinationWarehouse']);
        }

        self::assertNullableArray($item['inboundReceipt']);

        self::assertIsArray($item['receiptItems']);
        if (count($item['receiptItems'])) {
            self::assertArrayHasKeys(['id', 'quantity', 'status', 'inventory'], $item['receiptItems'][0]);
            self::assertArrayHasKeys(['id'], $item['receiptItems'][0]['inventory']);
        }
    }

    public function testStoreGoodReceiptValidationError(): void
    {
        $warehouse    = $this->manager->getRepository(Warehouse::class)->findOneBy([]);
        $sellePackage = $this->manager->getRepository(SellerPackage::class)->findOneBy([]);
        $sellePackage->setStatus(SellerPackageStatusDictionary::RECEIVED);
        $this->manager->flush();

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.receipt.store.good.receipt'), [
                 "sellerPackage" => $sellePackage->getId(),
                 "warehouse"     => $warehouse->getId(),
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKey('sellerPackage', $item);
        self::assertIsArray($item['sellerPackage']);
        self::assertEquals('Receipt was created for this seller package before!', $item['sellerPackage'][0]);
    }

    public function testStoreManualReceipt(): void
    {
        $sourceWarehouse = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test1",
        ]);
        $destWarehouse   = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test2",
        ]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.receipt.store.manual.receipt'), [
                 "sourceWarehouse"      => $sourceWarehouse->getId(),
                 "destinationWarehouse" => $destWarehouse->getId(),
                 "type"                 => ReceiptTypeDictionary::STOCK_TRANSFER,
                 "description"          => "test",
             ]);

        self::assertResponseStatusCodeSame(201);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'type',
            'referenceType',
            'description',
            'status',
            'sourceWarehouse',
            'destinationWarehouse',
            'inboundReceipt',
            'receiptItems',
        ], $item);

        self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);

        self::assertIsNumeric($item['id']);
        self::assertIsString($item['type']);
        self::assertNullableString($item['referenceType']);
        self::assertNullableString($item['description']);
        self::assertIsString($item['status']);

        self::assertNullableArray($item['sourceWarehouse']);
        if (isset($item['sourceWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        }

        self::assertNullableArray($item['destinationWarehouse']);
        if (isset($item['destinationWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['destinationWarehouse']);
        }

        self::assertNullableArray($item['inboundReceipt']);

        self::assertIsArray($item['receiptItems']);
        if (count($item['receiptItems'])) {
            self::assertArrayHasKeys(['id', 'quantity', 'status', 'inventory'], $item['receiptItems'][0]);
            self::assertArrayHasKeys(['id'], $item['receiptItems'][0]['inventory']);
        }
    }

    public function testStoreManualReceiptValidationError(): void
    {
        $sourceWarehouse = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test1",
        ]);
        $destWarehouse   = null;

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.receipt.store.manual.receipt'), [
                 "sourceWarehouse"      => $sourceWarehouse->getId(),
                 "destinationWarehouse" => $destWarehouse,
                 "type"                 => ReceiptTypeDictionary::STOCK_TRANSFER,
                 "costCenter"           => 1,
                 "description"          => "test",
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKey('destinationWarehouse', $item);
        self::assertIsArray($item['destinationWarehouse']);
        self::assertEquals('This value should not be blank.', $item['destinationWarehouse'][0]);

        self::assertArrayHasKey('costCenter', $item);
        self::assertIsArray($item['costCenter']);
        self::assertEquals('This value should be blank.', $item['costCenter'][0]);
    }

    public function testUpdateSuccessfully(): void
    {
        $receipt             = $this->manager->getRepository(STOutboundReceipt::class)->findOneBy([
            "description" => "test5",
        ]);
        $sourceWarehouse     = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test1",
        ]);
        $destWarehouse       = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test2",
        ]);
        $modifiedDescription = "test5";

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/receipts/manual/{$receipt->getId()}", [
                 "sourceWarehouse"      => $sourceWarehouse->getId(),
                 "destinationWarehouse" => $destWarehouse->getId(),
                 "description"          => $modifiedDescription,
                 "type"                 => ReceiptTypeDictionary::STOCK_TRANSFER,
             ]);

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'type',
            'referenceType',
            'description',
            'status',
            'sourceWarehouse',
            'destinationWarehouse',
            'inboundReceipt',
            'receiptItems',
            'itemBatches',
        ], $item);

        self::assertEquals($modifiedDescription, $item['description']);

        self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        self::assertArrayHasKeys(['id', 'title'], $item['destinationWarehouse']);

        self::assertIsNumeric($item['id']);
        self::assertIsString($item['type']);
        self::assertNullableString($item['referenceType']);
        self::assertNullableString($item['description']);
        self::assertIsString($item['status']);

        self::assertNullableArray($item['sourceWarehouse']);
        if (isset($item['sourceWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        }

        self::assertNullableArray($item['destinationWarehouse']);
        if (isset($item['destinationWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['destinationWarehouse']);
        }

        self::assertNullableArray($item['inboundReceipt']);

        self::assertIsArray($item['receiptItems']);
        if (count($item['receiptItems'])) {
            self::assertArrayHasKeys(['id', 'quantity', 'status', 'inventory'], $item['receiptItems'][0]);
            self::assertArrayHasKeys(['id'], $item['receiptItems'][0]['inventory']);
        }

        self::assertIsArray($item['itemBatches']);
        if (count($item['itemBatches'])) {
            self::assertArrayHasKeys(['id', 'quantity'], $item['itemBatches'][0]);
        }
    }

    public function testUpdateReceiptErrorWhenStatusApproved(): void
    {
        $receipt             = $this->manager->getRepository(GINoneReceipt::class)->findOneBy([
            "status" => ReceiptStatusDictionary::APPROVED,
        ]);
        $sourceWarehouse     = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test1",
        ]);
        $destWarehouse       = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test2",
        ]);
        $modifiedDescription = "test5";

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/receipts/manual/{$receipt->getId()}", [
                 "sourceWarehouse"      => $sourceWarehouse->getId(),
                 "destinationWarehouse" => $destWarehouse->getId(),
                 "description"          => $modifiedDescription,
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertEmpty($response['results']);
        self::assertFalse($response['succeed']);
        self::assertEquals("You can only edit receipt with DRAFT status!", $response['message']);
    }

    public function testUpdateReceiptErrorWhenReceiptIsNotNoneReferenceReceipt(): void
    {
        $receipt             = $this->manager->getRepository(STInboundReceipt::class)->findOneBy([]);
        $sourceWarehouse     = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test1",
        ]);
        $destWarehouse       = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test2",
        ]);
        $modifiedDescription = "test5";

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/receipts/manual/{$receipt->getId()}", [
                 "sourceWarehouse"      => $sourceWarehouse->getId(),
                 "destinationWarehouse" => $destWarehouse->getId(),
                 "description"          => $modifiedDescription,
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertEmpty($response['results']);
        self::assertFalse($response['succeed']);
        self::assertEquals("You can only edit none receipt!", $response['message']);
    }

    public function testUpdateReceiptErrorWhenHasItems(): void
    {
        $receipt             = $this->manager->getRepository(STOutboundReceipt::class)->findOneBy([
            "description" => "test4",
        ]);
        $sourceWarehouse     = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test1",
        ]);
        $destWarehouse       = $this->manager->getRepository(Warehouse::class)->findOneBy([
            "title" => "test2",
        ]);
        $modifiedDescription = "test5";

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/receipts/manual/{$receipt->getId()}", [
                 "sourceWarehouse"      => $sourceWarehouse->getId(),
                 "destinationWarehouse" => $destWarehouse->getId(),
                 "description"          => $modifiedDescription,
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertEmpty($response['results']);
        self::assertFalse($response['succeed']);
        self::assertEquals("it's not possible edit a receipt while it has items !", $response['message']);
    }

    public function testUpdateReceiptErrorValidation(): void
    {
        $receipt             = $this->manager->getRepository(STOutboundReceipt::class)->findOneBy([
            "description" => "test5",
        ]);
        $sourceWarehouse     = null;
        $destWarehouse       = null;
        $modifiedDescription = "test5";

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/receipts/manual/{$receipt->getId()}", [
                 "sourceWarehouse"      => $sourceWarehouse,
                 "destinationWarehouse" => $destWarehouse,
                 "type"                 => ReceiptTypeDictionary::STOCK_TRANSFER,
                 "description"          => $modifiedDescription,
                 "costCenter"           => 1,
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];

        self::assertArrayHasKey('sourceWarehouse', $item);
        self::assertIsArray($item['sourceWarehouse']);
        self::assertEquals('This value should not be blank.', $item['sourceWarehouse'][0]);

        self::assertArrayHasKey('destinationWarehouse', $item);
        self::assertIsArray($item['destinationWarehouse']);
        self::assertEquals('This value should not be blank.', $item['destinationWarehouse'][0]);

        self::assertArrayHasKey('costCenter', $item);
        self::assertIsArray($item['costCenter']);
        self::assertEquals('This value should be blank.', $item['costCenter'][0]);
    }

    public function testDeleteSuccessfully(): void
    {
        $receipt = $this->manager->getRepository(STOutboundReceipt::class)->findOneBy([
            "description" => "test5",
        ]);

        $client = $this->loginAs($this->admin)
                       ->sendRequest('DELETE', "/admin/receipts/{$receipt->getId()}");

        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $response = $this->getControllerResponse();
        self::assertArrayHasKey('succeed', $response);
        self::assertArrayHasKey('message', $response);
        self::assertArrayHasKey('results', $response);
        self::assertArrayHasKey('metas', $response);

        $result = $response['results'];
        self::assertArrayHasKey('id', $result);
    }

    public function testDeleteFailedWhenStatusIsApproved(): void
    {
        $receipt = $this->manager->getRepository(GINoneReceipt::class)->findOneBy([
            "status" => ReceiptStatusDictionary::APPROVED,
        ]);

        $client = $this->loginAs($this->admin)
                       ->sendRequest('DELETE', "/admin/receipts/{$receipt->getId()}");

        self::assertEquals(422, $client->getResponse()->getStatusCode());

        $response = $this->getControllerResponse();
        self::assertResponseEnvelope($response);
        self::assertEmpty($response['results']);
        self::assertFalse($response['succeed']);
        self::assertEquals("You can only delete a receipt with DRAFT status!", $response['message']);
    }

    public function testDeleteFailedWhenHasItems(): void
    {
        $receipt = $this->manager->getRepository(STOutboundReceipt::class)->findOneBy([
            "description" => "test4",
        ]);

        $client = $this->loginAs($this->admin)
                       ->sendRequest('DELETE', "/admin/receipts/{$receipt->getId()}");

        self::assertEquals(422, $client->getResponse()->getStatusCode());

        $response = $this->getControllerResponse();
        self::assertResponseEnvelope($response);
        self::assertEmpty($response['results']);
        self::assertFalse($response['succeed']);
        self::assertEquals("it's not possible delete a receipt while it has items!", $response['message']);
    }

    public function testApproveManualReceiptStatusSuccessfully(): void
    {
        $receipt = $this->manager->getRepository(STOutboundReceipt::class)->findOneBy([
            "status" => "DRAFT",
        ]);
        $status  = ReceiptStatusDictionary::APPROVED;

        $this->loginAs($this->admin)
             ->sendRequest('PATCH', "/admin/receipts/manual/{$receipt->getId()}/approve");

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'type',
            'referenceType',
            'description',
            'status',
            'sourceWarehouse',
            'destinationWarehouse',
            'inboundReceipt',
            'receiptItems',
            'itemBatches',
        ], $item);

        self::assertEquals($status, $item['status']);

        self::assertIsNumeric($item['id']);
        self::assertIsString($item['type']);
        self::assertNullableString($item['referenceType']);
        self::assertNullableString($item['description']);
        self::assertIsString($item['status']);

        self::assertNullableArray($item['sourceWarehouse']);
        if (isset($item['sourceWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        }

        self::assertNullableArray($item['destinationWarehouse']);
        if (isset($item['destinationWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['destinationWarehouse']);
        }

        self::assertNullableArray($item['inboundReceipt']);

        self::assertIsArray($item['receiptItems']);
        if (count($item['receiptItems'])) {
            foreach ($item['receiptItems'] as $receiptItem) {
                self::assertArrayHasKeys(['id', 'quantity', 'status', 'inventory'], $receiptItem);
                self::assertArrayHasKeys(['id'], $receiptItem['inventory']);
                self::assertEquals($status, $receiptItem['status']);
            }
        }

        self::assertIsArray($item['itemBatches']);
        if (count($item['itemBatches'])) {
            self::assertArrayHasKeys(['id', 'quantity'], $item['itemBatches'][0]);
        }
    }

    public function testApproveManualReceiptStatusIsNotValid(): void
    {
        $receipt = $this->manager->getRepository(GRMarketPlacePackageReceipt::class)->findOneBy([
            "status" => ReceiptStatusDictionary::APPROVED,
        ]);

        $client = $this->loginAs($this->admin)
                       ->sendRequest('PATCH', "/admin/receipts/manual/{$receipt->getId()}/approve");

        self::assertEquals(422, $client->getResponse()->getStatusCode());

        $response = $this->getControllerResponse();
        self::assertResponseEnvelope($response);
        self::assertEmpty($response['results']);
        self::assertFalse($response['succeed']);
        self::assertEquals("You can only edit none receipt!", $response['message']);
    }

    public function testUpdateStatusReadyToPick(): void
    {
        $receipt = $this->manager->getRepository(GINoneReceipt::class)->findOneBy([
            "status" => ReceiptStatusDictionary::APPROVED,
        ]);

        $this->loginAs($this->admin)
             ->sendRequest(
                 'POST',
                 $this->route('admin.receipt.update.status.ready.to.pick', ['id' => $receipt->getId()])
             );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();
        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKeys([
            'id',
            'type',
            'referenceType',
            'description',
            'status',
            'sourceWarehouse',
            'destinationWarehouse',
            'inboundReceipt',
            'receiptItems',
        ], $item);

        self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);

        self::assertIsNumeric($item['id']);
        self::assertIsString($item['type']);
        self::assertNullableString($item['referenceType']);
        self::assertNullableString($item['description']);
        self::assertIsString($item['status']);

        self::assertNullableArray($item['sourceWarehouse']);
        if (isset($item['sourceWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);
        }

        self::assertNullableArray($item['destinationWarehouse']);
        if (isset($item['destinationWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['destinationWarehouse']);
        }

        self::assertNullableArray($item['inboundReceipt']);

        self::assertIsArray($item['receiptItems']);
        if (count($item['receiptItems'])) {
            self::assertArrayHasKeys(['id', 'quantity', 'status', 'inventory'], $item['receiptItems'][0]);
            self::assertArrayHasKeys(['id'], $item['receiptItems'][0]['inventory']);
        }
    }

    public function testStoreSTInboundReceipt(): void
    {
        /** @var STOutboundReceipt $sourceReceipt */
        $sourceReceipt = $this->manager->getRepository(STOutboundReceipt::class)->findOneBy([
            "description" => "test10",
        ]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.receipt.store.st-inbound.receipt'), [
                 "outboundReceipt" => $sourceReceipt->getId(),
             ]);

        self::assertResponseStatusCodeSame(201);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'type',
            'referenceType',
            'description',
            'status',
            'sourceWarehouse',
            'destinationWarehouse',
            'inboundReceipt',
            'receiptItems',
        ], $item);

        self::assertArrayHasKeys(['id', 'title'], $item['sourceWarehouse']);

        self::assertIsNumeric($item['id']);
        self::assertIsString($item['type']);
        self::assertEquals("ST_INBOUND", $item['referenceType']);
        self::assertNullableString($item['description']);
        self::assertIsString($item['status']);

        self::assertNullableArray($item['destinationWarehouse']);
        if (isset($item['destinationWarehouse'])) {
            self::assertArrayHasKeys(['id', 'title'], $item['destinationWarehouse']);
        }

        self::assertNullableArray($item['inboundReceipt']);

        self::assertIsArray($item['receiptItems']);
        self::assertEquals(1, count($item['receiptItems']));

        self::assertArrayHasKeys(
            ['id', 'quantity', 'status', 'inventory', "receiptItemBatches", "receiptItemSerials"],
            $item['receiptItems'][0]
        );
        self::assertArrayHasKeys(
            ['id', 'quantity'],
            $item['receiptItems'][0]['receiptItemBatches'][0]["itemBatch"]
        );
        self::assertArrayHasKeys(
            ['id', 'serial', 'status'],
            $item['receiptItems'][0]['receiptItemSerials'][0]["itemSerial"]
        );
        self::assertArrayHasKeys(['id', 'color', 'guarantee', 'size'], $item['receiptItems'][0]['inventory']);

        self::assertEquals($item['id'], $sourceReceipt->getInboundReceipt()->getId());

        /** @var ReceiptItem $sourceReceiptItem */
        $sourceReceiptItem = $sourceReceipt->getReceiptItems()->first();

        self::assertEquals($item['receiptItems'][0]['status'], "APPROVED");
        self::assertEquals($item['receiptItems'][0]['inventory']['id'], $sourceReceiptItem->getInventory()->getId());
        self::assertEquals($item['receiptItems'][0]['quantity'], $sourceReceiptItem->getQuantity());
        self::assertEquals(
            $item['receiptItems'][0]['receiptItemBatches'][0]["itemBatch"]["id"],
            $sourceReceiptItem->getReceiptItemBatches()->first()->getItemBatch()->getId()
        );
        self::assertEquals(
            $item['receiptItems'][0]['receiptItemSerials'][0]["itemSerial"]["id"],
            $sourceReceiptItem->getReceiptItemSerials()->first()->getItemSerial()->getId()
        );
    }

    public function testStoreSTInboundReceiptValidationError(): void
    {
        $sourceReceipt = $this->manager->getRepository(Receipt::class)->findOneBy([
            "description" => "test3",
        ]);

        $this->loginAs($this->admin)
             ->sendRequest('POST', $this->route('admin.receipt.store.st-inbound.receipt'), [
                 "outboundReceipt" => $sourceReceipt->getId(),
             ]);

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $item = $response['results'];
        self::assertArrayHasKey('outboundReceipt', $item);
        self::assertIsArray($item['outboundReceipt']);
        self::assertEquals('The selected choice is invalid.', $item['outboundReceipt'][0]);
    }
}
