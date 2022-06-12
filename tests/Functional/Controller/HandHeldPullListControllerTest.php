<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\PullListPriorityDictionary;
use App\Dictionary\PullListStatusDictionary;
use App\Dictionary\StorageBinTypeDictionary;
use App\Entity\PullList;
use App\Entity\WarehouseStorageBin;
use App\Repository\PullListRepository;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\Persistence\ObjectRepository;

class HandHeldPullListControllerTest extends FunctionalTestCase
{
    protected PullListRepository|ObjectRepository|null $pullListRepository;

    protected ObjectRepository|null $storageBinRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pullListRepository   = $this->manager->getRepository(PullList::class);
        $this->storageBinRepository = $this->manager->getRepository(WarehouseStorageBin::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->pullListRepository   = null;
        $this->storageBinRepository = null;
    }

    public function testScanStorageBinSuccess(): void
    {
        $bin = $this->storageBinRepository->findOneBy(['type' => StorageBinTypeDictionary::AISLE]);

        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.hand-held.pull-list.scan.bin.serial'),
            [
                'storageBin' => $bin->getSerial(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertTrue($response['succeed']);
        self::assertEquals('Scan storage bin serial has done successfully', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'type',
            'serial',
        ], $item);
    }

    public function testScanStorageBinWithWrongSerialBin(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.hand-held.pull-list.scan.bin.serial'),
            [
                'storageBin' => "wrongSerialBin",
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertFalse($response['succeed']);
        self::assertNotEmpty($response['results']);
        self::assertEquals('The storageBin does not exist', $response['results']['storageBin'][0]);
    }

    public function testStow(): void
    {
        $pullList   = $this->pullListRepository->findOneBy([
            'locator'  => $this->admin,
            'status'   => PullListStatusDictionary::STOWING,
            'priority' => PullListPriorityDictionary::HIGH,
        ]);
        $storageBin = $this->storageBinRepository->findOneBy(['type' => StorageBinTypeDictionary::AISLE]);

        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            "/admin/hand-held/pull-lists/stow/{$pullList->getId()}",
            [
                'itemSerial' => $pullList->getItems()[0]->getReceiptItem()
                                                        ->getReceiptItemSerials()[1]->getItemSerial()->getSerial(),
                'storageBin' => $storageBin->getSerial(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys(['id', 'status', 'priority', 'items'], $item);
        self::assertArrayHasKeys(['id', 'status', 'quantity', 'remainQuantity'], $item['items'][0]);

        self::assertEquals("CLOSED", $item['status']);
        self::assertEquals("CLOSED", $item['items'][0]['status']);
        self::assertEquals(0, $item['items'][0]['remainQuantity']);
    }

    public function testConfirmAll(): void
    {
        $pullList   = $this->pullListRepository->findOneBy([
            'locator'  => $this->admin,
            'status'   => PullListStatusDictionary::CONFIRMED_BY_LOCATOR,
            'priority' => PullListPriorityDictionary::LOW,
        ]);
        $storageBin = $this->storageBinRepository->findOneBy(['type' => StorageBinTypeDictionary::AISLE]);

        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            "/admin/hand-held/pull-lists/confirm-all/items/{$pullList->getItems()[0]->getId()}",
            [
                'storageBin' => $storageBin->getSerial(),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys(['id', 'status', 'priority', 'items'], $item);
        self::assertArrayHasKeys(['id', 'status', 'quantity', 'remainQuantity'], $item['items'][0]);
    }

    public function testItCanNotGetActivePullListToLocate(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.hand-held.pull-list.active-for-locate'),
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Response successfully returned', $response['message']);
        self::assertEmpty($response['results']);
        self::assertEmpty($response['metas']);
    }

    public function testItCanGetActivePullListToLocate(): void
    {
        $confirmedPullList = $this->pullListRepository->findOneBy([
            'locator' => $this->admin,
            'status'  => PullListStatusDictionary::CONFIRMED_BY_LOCATOR,
        ]);
        $stowingPullLists  = $this->pullListRepository->findBy([
            'locator' => $this->admin,
            'status'  => PullListStatusDictionary::STOWING,
        ]);

        $confirmedPullList->setStatus(PullListStatusDictionary::CLOSED);

        foreach ($stowingPullLists as $stowingPullList) {
            $stowingPullList->setStatus(PullListStatusDictionary::CLOSED);
        }

        $this->manager->flush();

        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.hand-held.pull-list.active-for-locate'),
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Response successfully returned', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['id', 'warehouse', 'priority', 'items', 'quantity', 'remainingQuantity'], $results);
        self::assertArrayHasKeys(['title'], $results['warehouse']);

        foreach ($results['items'] as $item) {
            self::assertArrayHasKeys(['id', 'receiptItem', 'quantity', 'remainQuantity'], $item);
            self::assertArrayHasKeys(['inventory'], $item['receiptItem']);
            $inventory = $item['receiptItem']['inventory'];
            self::assertArrayHasKeys(['id', 'color', 'guarantee', 'size', 'product'], $inventory);
            self::assertArrayHasKeys(['id', 'title'], $inventory['product']);
        }
    }

    public function testItCanShowActiveList(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.hand-held.pull-list.show-active-list'),
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Response successfully returned', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['id', 'warehouse', 'priority', 'items', 'quantity', 'remainingQuantity'], $results);
        self::assertArrayHasKeys(['title'], $results['warehouse']);

        foreach ($results['items'] as $item) {
            self::assertArrayHasKeys(['id', 'receiptItem', 'quantity', 'remainQuantity'], $item);
            self::assertArrayHasKeys(['inventory'], $item['receiptItem']);
            $inventory = $item['receiptItem']['inventory'];
            self::assertArrayHasKeys(['id', 'color', 'guarantee', 'size', 'product'], $inventory);
            self::assertArrayHasKeys(['id', 'title'], $inventory['product']);
        }
    }
}
