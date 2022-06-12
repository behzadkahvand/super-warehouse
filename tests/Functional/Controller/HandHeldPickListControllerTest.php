<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Dictionary\PickListStatusDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Repository\PickListRepository;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\Persistence\ObjectRepository;

class HandHeldPickListControllerTest extends FunctionalTestCase
{
    protected PickListRepository|ObjectRepository|null $pickListRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pickListRepository = $this->manager->getRepository(PickList::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->pickListRepository = null;
    }

    public function testShow(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.hand.held.pick.list.show', ['receiptType' => ReceiptTypeDictionary::STOCK_TRANSFER]),
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'status',
            'priority',
            'quantity',
            'remainedQuantity',
            'storageBin',
            'receiptItem',
        ], $item);

        self::assertIsArray($item['storageBin']);
        self::assertIsArray($item['receiptItem']);
        self::assertIsArray($item['receiptItem']['inventory']);
        self::assertIsArray($item['receiptItem']['inventory']['product']);

        self::assertArrayHasKeys(['id', 'serial'], $item['storageBin']);
        self::assertArrayHasKeys(['id', 'inventory'], $item['receiptItem']);
        self::assertArrayHasKeys(['id', 'color', 'size', 'product'], $item['receiptItem']['inventory']);
        self::assertArrayHasKeys(
            ['id', 'title', 'length', 'width', 'height', 'weight', 'mainImage'],
            $item['receiptItem']['inventory']['product']
        );

        self::assertIsInt($item['id']);
        self::assertIsInt($item['quantity']);
        self::assertIsInt($item['remainedQuantity']);
        self::assertIsString($item['priority']);
        self::assertIsString($item['status']);
    }

    public function testConfirm(): void
    {
        $notConfirmedPickList = $this->pickListRepository->findOneBy(['picker' => null]);

        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.hand.held.pick.list.confirm'),
            [
                'items' => [$notConfirmedPickList->getId()],
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertTrue($response['succeed']);
        self::assertEquals('Confirm pick lists has done successfully', $response['message']);
        self::assertEmpty($response['results']);
        self::assertEquals([], $response['metas']);
    }

    public function testScanBinSerialSuccess(): void
    {
        $confirmedPickList = $this->pickListRepository->findOneBy(['picker' => $this->admin,
                                                                   'status' => PickListStatusDictionary::PICKING]);
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.hand.held.pick.list.scan.bin.serial'),
            [
                'binSerial' => $confirmedPickList->getStorageBin()->getSerial(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertTrue($response['succeed']);
        self::assertEquals('Scan storage bin serial has done successfully', $response['message']);
        self::assertEmpty($response['results']);
        self::assertEquals([], $response['metas']);
    }

    public function testScanBinSerialWrongSerialBin(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.hand.held.pick.list.scan.bin.serial'),
            [
                'binSerial' => "wrongSerialBin",
            ]
        );

        self::assertResponseStatusCodeSame(400);

        $response = $this->getControllerResponse();

        self::assertExceptionResponseEnvelope($response);

        self::assertEquals('An error occurred', $response['title']);
        self::assertEquals(400, $response['status']);
        self::assertEquals('You have not any active picklist in given storageBin!', $response['detail']);
    }

    public function testPickSuccess(): void
    {
        $confirmedPickList = $this->pickListRepository->findOneBy(['picker' => $this->admin,
                                                                   'status' => PickListStatusDictionary::PICKING]);
        $itemSerial        = $this->manager->getRepository(ItemSerial::class)
                                           ->findOneBy(['inventory'           => $confirmedPickList->getReceiptItem()
                                                                                                   ->getInventory(),
                                                        'warehouseStorageBin' => $confirmedPickList->getStorageBin(),
                                                        'status'              => ItemSerialStatusDictionary::SALABLE_STOCK,
                                           ]);
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route(
                'admin.hand.held.pick.list.pick',
                [
                    'id'         => $confirmedPickList->getId(),
                    'itemSerial' => $itemSerial->getSerial(),
                ]
            ),
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'status',
            'priority',
            'quantity',
            'remainedQuantity',
        ], $item);
    }

    public function testPickWithWrongSerial(): void
    {
        $confirmedPickList = $this->pickListRepository->findOneBy(['picker' => $this->admin,
                                                                   'status' => PickListStatusDictionary::PICKING]);
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route(
                'admin.hand.held.pick.list.pick',
                [
                    'id'         => $confirmedPickList->getId(),
                    'itemSerial' => "wrongItemSerial",
                ]
            ),
        );

        self::assertResponseStatusCodeSame(400);

        $response = $this->getControllerResponse();

        self::assertExceptionResponseEnvelope($response);

        self::assertEquals('An error occurred', $response['title']);
        self::assertEquals(400, $response['status']);
        self::assertEquals('There is no any itemSerial for given serial!', $response['detail']);
    }
}
