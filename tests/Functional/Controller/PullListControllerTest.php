<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\PullListPriorityDictionary;
use App\Dictionary\PullListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\Warehouse;
use App\Repository\PullListItemRepository;
use App\Repository\PullListRepository;
use App\Repository\ReceiptRepository;
use App\Repository\WarehouseRepository;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\Persistence\ObjectRepository;

class PullListControllerTest extends FunctionalTestCase
{
    protected ?Warehouse $warehouse;

    protected ?PullList $pullList;

    protected ?PullListItem $pullListItem;

    protected WarehouseRepository|ObjectRepository|null $warehouseRepo;

    protected PullListRepository|ObjectRepository|null $pullListRepo;

    protected PullListItemRepository|ObjectRepository|null $pullListItemRepo;

    protected ReceiptRepository|ObjectRepository|null $receiptRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warehouseRepo = $this->manager->getRepository(Warehouse::class);

        $this->warehouse = $this->warehouseRepo->findOneBy([]);

        $this->pullListRepo = $this->manager->getRepository(PullList::class);

        $this->pullList = $this->pullListRepo->findOneBy([]);

        $this->pullListItemRepo = $this->manager->getRepository(PullListItem::class);

        $this->pullListItem = $this->pullListItemRepo->findOneBy([]);

        $this->receiptRepo = $this->manager->getRepository(Receipt::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->warehouse     = null;
        $this->pullList      = null;
        $this->warehouseRepo = null;
        $this->pullListRepo  = null;
        $this->receiptRepo   = null;
    }

    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.pullList.index')
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertNotEmpty($response['results']);
        self::assertArrayHasKeys(['page', 'perPage', 'totalItems', 'totalPages'], $response['metas']);

        $results = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'warehouse',
            'priority',
            'locator',
            'quantity',
            'remainingQuantity',
            'status'
        ], $results);
        if ($results['warehouse']) {
            self::assertArrayHasKeys(['id', 'title'], $results['warehouse']);
        }
        if ($results['locator']) {
            self::assertArrayHasKeys(['id', 'email'], $results['locator']);
        }
    }

    public function testValidationFailureStorePullList(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pullList.store.manual'),
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals('Validation error has been detected!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['warehouse', 'priority'], $results);
        self::assertEquals('This value should not be blank.', $results['warehouse'][0]);
        self::assertEquals('This value should not be blank.', $results['priority'][0]);
    }

    public function testItCanStorePullList(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pullList.store.manual'),
            [
                'warehouse' => $this->warehouse->getId(),
                'priority'  => PullListPriorityDictionary::HIGH,
            ]
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Pull list is added successfully!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['id'], $results);
    }

    public function testValidationFailureOnSearchReceiptItemWhenFilterIsInvalid(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route(
                'admin.pullList.receipt-item.add-list',
                [
                    'id'     => $this->pullList->getId(),
                    'filter' => [
                        'status' => ReceiptStatusDictionary::DRAFT,
                    ],
                ]
            ),
        );

        self::assertResponseStatusCodeSame(400);

        $response = $this->getControllerResponse();

        self::assertExceptionResponseEnvelope($response);

        self::assertEquals('An error occurred', $response['title']);
        self::assertEquals(400, $response['status']);
        self::assertEquals('Receipt Item filters is invalid!', $response['detail']);
    }

    public function testValidationFailureOnSearchReceiptItemWhenSortIsInvalid(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route(
                'admin.pullList.receipt-item.add-list',
                [
                    'id'   => $this->pullList->getId(),
                    'sort' => [
                        '-status',
                    ],
                ]
            ),
        );

        self::assertResponseStatusCodeSame(400);

        $response = $this->getControllerResponse();

        self::assertExceptionResponseEnvelope($response);

        self::assertEquals('An error occurred', $response['title']);
        self::assertEquals(400, $response['status']);
        self::assertEquals('Receipt Item sorts is invalid!', $response['detail']);
    }

    public function testItCanSearchReceiptItemWithoutFiltersAndSorts(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route(
                'admin.pullList.receipt-item.add-list',
                [
                    'id' => $this->pullList->getId(),
                ]
            ),
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $results = $response['results'];

        self::assertArrayHasKeys(['id', 'quantity', 'receipt', 'inventory',], $results[0]);
        self::assertArrayHasKeys(['id', 'product',], $results[0]['inventory']);
        self::assertArrayHasKeys(['id',], $results[0]['inventory']['product']);
        self::assertArrayHasKeys(['id',], $results[0]['receipt']);
    }

    public function testItCanSearchReceiptItemWithInventoryIdFilters(): void
    {
        $inventory = $this->manager->getRepository(Warehouse::class)->findOneBy([]);

        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route(
                'admin.pullList.receipt-item.add-list',
                [
                    'id'          => $this->pullList->getId(),
                    'inventoryId' => $inventory->getId(),
                ]
            ),
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $results = $response['results'];

        self::assertArrayHasKeys(['id', 'quantity', 'receipt', 'inventory',], $results[0]);
        self::assertArrayHasKeys(['id', 'product',], $results[0]['inventory']);
        self::assertArrayHasKeys(['id',], $results[0]['inventory']['product']);
        self::assertArrayHasKeys(['id',], $results[0]['receipt']);
    }

    public function testItCanUpdatePullList(): void
    {
        $warehouse = $this->warehouseRepo->findOneBy(["title" => "test2"]);
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.pullList.update', [
                'id' => $this->pullList->getId(),
            ]),
            [
                'warehouse' => $warehouse->getId(),
                'priority'  => PullListPriorityDictionary::LOW,
            ]
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Pull list is updated successfully!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['id', 'warehouse', 'priority', 'locator'], $results);

        self::assertEquals($warehouse->getId(), $results['warehouse']['id']);
        self::assertEquals(PullListPriorityDictionary::LOW, $results['priority']);
    }

    public function testUpdatePullListWarehouseFailedWhenStatusIsNotDraft(): void
    {
        $pullList = $this->pullListRepo->findOneBy(['status' => PullListStatusDictionary::STOWING]);

        $warehouse = $this->warehouseRepo->findOneBy(["title" => "test1"]);

        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.pullList.update', [
                'id' => $pullList->getId(),
            ]),
            [
                'warehouse' => $warehouse->getId(),
                'priority'  => PullListPriorityDictionary::MEDIUM,
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals(
            'You can only edit pull-list warehouse when pull-list status is DRAFT and it does not have any items!',
            $response['message']
        );
        self::assertEmpty($response['results']);
        self::assertEquals([], $response['metas']);
    }

    public function testUpdatePullListWarehouseFailedWhenItHasItems(): void
    {
        $pullList = $this->pullListRepo->findOneBy([
            'status'   => PullListStatusDictionary::DRAFT,
            "priority" => PullListPriorityDictionary::LOW,
        ]);

        $warehouse = $this->warehouseRepo->findOneBy(["title" => "test1"]);

        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.pullList.update', [
                'id' => $pullList->getId(),
            ]),
            [
                'warehouse' => $warehouse->getId(),
                'priority'  => PullListPriorityDictionary::LOW,
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals(
            'You can only edit pull-list warehouse when pull-list status is DRAFT and it does not have any items!',
            $response['message']
        );
        self::assertEmpty($response['results']);
        self::assertEquals([], $response['metas']);
    }

    public function testUpdatePullListPriorityFailedWhenStatusIsNotDraftOrSendToLocator(): void
    {
        $pullList = $this->pullListRepo->findOneBy([
            'status' => PullListStatusDictionary::STOWING,
        ]);

        $warehouse = $this->warehouseRepo->findOneBy(["title" => "test2"]);

        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.pullList.update', [
                'id' => $pullList->getId(),
            ]),
            [
                'warehouse' => $warehouse->getId(),
                'priority'  => PullListPriorityDictionary::HIGH,
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals(
            'You can only edit pull-list priority when pull-list status is DRAFT or SENT_TO_LOCATOR!',
            $response['message']
        );
        self::assertEmpty($response['results']);
        self::assertEquals([], $response['metas']);
    }

    public function testDeletePullListSuccessfully(): void
    {
        $pullList = $this->pullListRepo->findOneBy([
            "status"   => PullListStatusDictionary::DRAFT,
            "priority" => PullListPriorityDictionary::LOW,
        ]);
        $this->loginAs($this->admin)->sendRequest(
            'DELETE',
            $this->route('admin.pullList.delete', [
                'id' => $pullList->getId(),
            ])
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        $result = $response['results'];
        self::assertArrayHasKey('id', $result);
    }

    public function testDeletePullListFailedWhenStatusIsNotDraft(): void
    {
        $pullList = $this->pullListRepo->findOneBy([
            "status" => PullListStatusDictionary::STOWING,
        ]);
        $this->loginAs($this->admin)->sendRequest(
            'DELETE',
            $this->route('admin.pullList.delete', [
                'id' => $pullList->getId(),
            ])
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals(
            'You can only delete a pull-list with DRAFT status!',
            $response['message']
        );
        self::assertEmpty($response['results']);
        self::assertEquals([], $response['metas']);
    }

    public function testValidationFailureOnAddingItemToPullList(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pullList.items.add', [
                'id' => $this->pullList->getId(),
            ]),
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals('Validation error has been detected!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['receiptItems'], $results);
        self::assertEquals('This value should not be blank.', $results['receiptItems'][0]);
    }

    public function testItCanAddItemsToPullList(): void
    {
        $receipts = $this->receiptRepo->findBy([
            'sourceWarehouse' => $this->pullList->getWarehouse(),
            'type'            => ReceiptTypeDictionary::GOOD_RECEIPT,
            'status'          => ReceiptStatusDictionary::READY_TO_STOW
        ]);

        $receiptItemIds = end($receipts)->getReceiptItems()
                                        ->map(fn(ReceiptItem $receiptItem) => $receiptItem->getId())
                                        ->toArray();

        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pullList.items.add', [
                'id' => $this->pullList->getId(),
            ]),
            [
                'receiptItems' => $receiptItemIds
            ]
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Pull list items is added successfully!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['id', 'warehouse', 'priority', 'status', 'items'], $results);
        self::assertArrayHasKeys(['id'], $results['warehouse']);

        foreach ($results['items'] as $item) {
            self::assertArrayHasKeys(['id', 'receipt', 'receiptItem', 'quantity', 'remainQuantity'], $item);
            self::assertArrayHasKeys(['id', 'type'], $item['receipt']);
            self::assertArrayHasKeys(['id', 'quantity'], $item['receiptItem']);

            self::assertEquals($item['quantity'], $item['remainQuantity']);
            self::assertEquals($item['quantity'], $item['receiptItem']['quantity']);
            self::assertEquals(PullListStatusDictionary::DRAFT, $item['status']);
        }
    }

    public function testValidationFailureOnAssignLocatorToPullList(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'PUT',
            $this->route('admin.pullList.locator.assign', [
                'id' => $this->pullList->getId(),
            ]),
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals('Validation error has been detected!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['locator'], $results);
        self::assertEquals('This value should not be blank.', $results['locator'][0]);
    }

    public function testItCanNotAssignLocatorToConfirmedByLocatorPullList(): void
    {
        $this->pullList->setStatus(PullListStatusDictionary::CONFIRMED_BY_LOCATOR);

        $this->manager->flush();

        $this->loginAs($this->admin)->sendRequest(
            'PUT',
            $this->route('admin.pullList.locator.assign', [
                'id' => $this->pullList->getId(),
            ]),
            [
                'locator' => $this->admin->getId(),
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals('You can only assign locator when pull-list status is DRAFT or SENT_TO_LOCATOR!', $response['message']);
        self::assertEquals([], $response['results']);
        self::assertEquals([], $response['metas']);
    }

    public function testItCanNotAssignLocatorToStowingPullList(): void
    {
        $this->pullList->setStatus(PullListStatusDictionary::STOWING);

        $this->manager->flush();

        $this->loginAs($this->admin)->sendRequest(
            'PUT',
            $this->route('admin.pullList.locator.assign', [
                'id' => $this->pullList->getId(),
            ]),
            [
                'locator' => $this->admin->getId(),
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals('You can only assign locator when pull-list status is DRAFT or SENT_TO_LOCATOR!', $response['message']);
        self::assertEquals([], $response['results']);
        self::assertEquals([], $response['metas']);
    }

    public function testItCanNotAssignLocatorToClosedPullList(): void
    {
        $this->pullList->setStatus(PullListStatusDictionary::CLOSED);

        $this->manager->flush();

        $this->loginAs($this->admin)->sendRequest(
            'PUT',
            $this->route('admin.pullList.locator.assign', [
                'id' => $this->pullList->getId(),
            ]),
            [
                'locator' => $this->admin->getId(),
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals('You can only assign locator when pull-list status is DRAFT or SENT_TO_LOCATOR!', $response['message']);
        self::assertEquals([], $response['results']);
        self::assertEquals([], $response['metas']);
    }

    public function testItCanAssignLocatorToPullList(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'PUT',
            $this->route('admin.pullList.locator.assign', [
                'id' => $this->pullList->getId(),
            ]),
            [
                'locator' => $this->admin->getId(),
            ]
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Pull list is assigned to locator successfully!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['id', 'warehouse', 'priority', 'locator',], $results);
        self::assertArrayHasKeys(['id', 'title',], $results['warehouse']);
        self::assertArrayHasKeys(['id', 'name', 'family',], $results['locator']);
    }

    public function testItCanGetPullListItemsByPullList(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'Get',
            $this->route('admin.pullList.items.index', [
                'id' => $this->pullListItem->getPullList()->getId(),
            ]),
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Response successfully returned', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys(['id', 'receipt', 'receiptItem', 'quantity', 'remainQuantity', 'status',], $results[0]);
        self::assertArrayHasKeys(['id',], $results[0]['receipt']);
        self::assertArrayHasKeys(['id',], $results[0]['receiptItem']);
    }

    public function testItCanNotSentPullListToLocator(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pullList.sent-to-locator', [
                'id' => $this->pullList->getId(),
            ]),
        );

        self::assertResponseStatusCodeSame(400);

        $response = $this->getControllerResponse();

        self::assertExceptionResponseEnvelope($response);

        self::assertEquals('An error occurred', $response['title']);
        self::assertEquals(400, $response['status']);
        self::assertEquals('Pull list has no items!', $response['detail']);
    }

    public function testItCanSentPullListToLocator(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pullList.sent-to-locator', [
                'id' => $this->pullListItem->getPullList()->getId(),
            ]),
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Pull list is sent to locator successfully!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'warehouse',
            'priority',
            'status',
            'quantity',
            'remainingQuantity',
            'locator',
        ], $results);
        self::assertArrayHasKeys(['id', 'title'], $results['warehouse']);
    }

    public function testItCanNotConfirmedPullListByLocator(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pullList.confirmed-by-locator', [
                'id' => $this->pullList->getId(),
            ]),
        );

        self::assertResponseStatusCodeSame(400);

        $response = $this->getControllerResponse();

        self::assertExceptionResponseEnvelope($response);

        self::assertEquals('An error occurred', $response['title']);
        self::assertEquals(400, $response['status']);
        self::assertEquals('Pull list not found for confirming!', $response['detail']);
    }

    public function testItCanConfirmedPullListByLocator(): void
    {
        $pullList = $this->pullListRepo->findOneBy([
            'locator' => $this->admin,
            'status'  => PullListStatusDictionary::SENT_TO_LOCATOR
        ]);

        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pullList.sent-to-locator', [
                'id' => $pullList->getId(),
            ]),
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertEquals('Pull list is sent to locator successfully!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'warehouse',
            'priority',
            'status',
            'quantity',
            'remainingQuantity',
            'locator',
        ], $results);
        self::assertArrayHasKeys(['id', 'title'], $results['warehouse']);
    }
}
