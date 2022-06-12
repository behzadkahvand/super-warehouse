<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\PickList;
use App\Entity\PickListBugReport;
use App\Tests\Functional\FunctionalTestCase;

class PickListControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route(
                'admin.pick.list.index',
                ['filter' => ['receiptItem.receipt.type' => ReceiptTypeDictionary::GOOD_ISSUE]]
            ),
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'priority',
            'quantity',
            'remainedQuantity',
            'status',
            'picker',
            'storageArea',
            'storageBin',
            'warehouse',
            'receiptItem',
        ], $item);

        self::assertIsArray($item['warehouse']);
        self::assertIsArray($item['storageBin']);
        self::assertIsArray($item['storageArea']);
        self::assertIsArray($item['picker']);
        self::assertIsArray($item['receiptItem']);

        self::assertArrayHasKey('id', $item['warehouse']);
        self::assertArrayHasKey('id', $item['storageBin']);
        self::assertArrayHasKey('id', $item['storageArea']);
        self::assertArrayHasKeys(['id', 'name', 'family'], $item['picker']);
        self::assertArrayHasKeys(['id', 'receipt', 'inventory'], $item['receiptItem']);

        self::assertIsInt($item['id']);
        self::assertIsInt($item['quantity']);
        self::assertIsInt($item['remainedQuantity']);
        self::assertIsString($item['priority']);
        self::assertIsString($item['status']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsInt($item['storageBin']['id']);
        self::assertIsInt($item['storageArea']['id']);
        self::assertIsInt($item['picker']['id']);
        self::assertIsString($item['picker']['name']);
        self::assertIsString($item['picker']['family']);
        self::assertIsInt($item['receiptItem']['id']);
        self::assertIsArray($item['receiptItem']['receipt']);
        self::assertIsArray($item['receiptItem']['inventory']);

        self::assertArrayHasKeys(['id', 'type'], $item['receiptItem']['receipt']);
        self::assertArrayHasKey('id', $item['receiptItem']['inventory']);
    }

    public function testStore(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pick.list.store'),
            [
                'quantity'        => 10,
                'promiseDateFrom' => date('Y-m-d', strtotime('1 October 2021')),
                'promiseDateTo'   => date('Y-m-d', strtotime('+1 day')),
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
    }

    public function testShowBugReports(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.pick.list.bug.report.index'),
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'quantity',
            'inventory',
            'status',
            'warehouse',
            'pickList',
        ], $item);

        self::assertIsArray($item['warehouse']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['pickList']);

        self::assertArrayHasKey('id', $item['inventory']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertArrayHasKeys([
            'id',
            'status',
            'priority',
            'picker',
            'storageBin',
            'storageArea',
        ], $item['pickList']);

        self::assertIsArray($item['pickList']['storageArea']);
        self::assertIsArray($item['pickList']['storageBin']);
        self::assertIsArray($item['pickList']['picker']);

        self::assertArrayHasKeys(['id', 'title'], $item['pickList']['storageArea']);
        self::assertArrayHasKeys(['id', 'serial'], $item['pickList']['storageBin']);
        self::assertArrayHasKeys(['id', 'name', 'family'], $item['pickList']['picker']);

        self::assertIsInt($item['id']);
        self::assertIsInt($item['quantity']);
        self::assertIsString($item['status']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsInt($item['inventory']['id']);
        self::assertIsInt($item['pickList']['id']);
        self::assertIsString($item['pickList']['status']);
        self::assertIsString($item['pickList']['priority']);
        self::assertIsInt($item['pickList']['storageBin']['id']);
        self::assertIsString($item['pickList']['storageBin']['serial']);
        self::assertIsInt($item['pickList']['storageArea']['id']);
        self::assertIsString($item['pickList']['storageArea']['title']);
        self::assertIsInt($item['pickList']['picker']['id']);
        self::assertIsString($item['pickList']['picker']['name']);
        self::assertIsString($item['pickList']['picker']['family']);
    }

    public function testStoreBugReport(): void
    {
        $pickLists = $this->manager->getRepository(PickList::class)->findAll();
        /** @var PickList $pickList */
        $pickList = collect($pickLists)
            ->first(fn(PickList $pickList) => empty($pickList->getPickListBugReport()) && !empty($pickList->getPicker()));

        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.pick.list.bug.report.store', ['id' => $pickList->getId()]),
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'quantity',
            'inventory',
            'status',
            'warehouse',
            'pickList',
        ], $item);

        self::assertIsArray($item['warehouse']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['pickList']);

        self::assertArrayHasKey('id', $item['inventory']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertArrayHasKeys([
            'id',
            'status',
            'priority',
            'picker',
            'storageBin',
            'storageArea',
        ], $item['pickList']);

        self::assertIsArray($item['pickList']['storageArea']);
        self::assertIsArray($item['pickList']['storageBin']);
        self::assertIsArray($item['pickList']['picker']);

        self::assertArrayHasKeys(['id', 'title'], $item['pickList']['storageArea']);
        self::assertArrayHasKeys(['id', 'serial'], $item['pickList']['storageBin']);
        self::assertArrayHasKeys(['id', 'name', 'family'], $item['pickList']['picker']);

        self::assertIsInt($item['id']);
        self::assertIsInt($item['quantity']);
        self::assertIsString($item['status']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsInt($item['inventory']['id']);
        self::assertIsInt($item['pickList']['id']);
        self::assertIsString($item['pickList']['status']);
        self::assertIsString($item['pickList']['priority']);
        self::assertIsInt($item['pickList']['storageBin']['id']);
        self::assertIsString($item['pickList']['storageBin']['serial']);
        self::assertIsInt($item['pickList']['storageArea']['id']);
        self::assertIsString($item['pickList']['storageArea']['title']);
        self::assertIsInt($item['pickList']['picker']['id']);
        self::assertIsString($item['pickList']['picker']['name']);
        self::assertIsString($item['pickList']['picker']['family']);
    }

    public function testBugReportUpdateStatus(): void
    {
        /** @var PickListBugReport $pickListBugReport */
        $pickListBugReport = $this->manager->getRepository(PickListBugReport::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route(
                'admin.pick.list.bug.report.update.status',
                ['pickListId' => $pickListBugReport->getPickList()->getId(), 'id' => $pickListBugReport->getId()]
            ),
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'quantity',
            'inventory',
            'status',
            'warehouse',
            'pickList',
        ], $item);

        self::assertIsArray($item['warehouse']);
        self::assertIsArray($item['inventory']);
        self::assertIsArray($item['pickList']);

        self::assertArrayHasKey('id', $item['inventory']);
        self::assertArrayHasKeys(['id', 'title'], $item['warehouse']);
        self::assertArrayHasKeys([
            'id',
            'status',
            'priority',
            'picker',
            'storageBin',
            'storageArea',
        ], $item['pickList']);

        self::assertIsArray($item['pickList']['storageArea']);
        self::assertIsArray($item['pickList']['storageBin']);
        self::assertIsArray($item['pickList']['picker']);

        self::assertArrayHasKeys(['id', 'title'], $item['pickList']['storageArea']);
        self::assertArrayHasKeys(['id', 'serial'], $item['pickList']['storageBin']);
        self::assertArrayHasKeys(['id', 'name', 'family'], $item['pickList']['picker']);

        self::assertIsInt($item['id']);
        self::assertIsInt($item['quantity']);
        self::assertIsString($item['status']);
        self::assertIsInt($item['warehouse']['id']);
        self::assertIsString($item['warehouse']['title']);
        self::assertIsInt($item['inventory']['id']);
        self::assertIsInt($item['pickList']['id']);
        self::assertIsString($item['pickList']['status']);
        self::assertIsString($item['pickList']['priority']);
        self::assertIsInt($item['pickList']['storageBin']['id']);
        self::assertIsString($item['pickList']['storageBin']['serial']);
        self::assertIsInt($item['pickList']['storageArea']['id']);
        self::assertIsString($item['pickList']['storageArea']['title']);
        self::assertIsInt($item['pickList']['picker']['id']);
        self::assertIsString($item['pickList']['picker']['name']);
        self::assertIsString($item['pickList']['picker']['family']);
    }
}
