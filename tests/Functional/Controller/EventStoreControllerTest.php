<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\FunctionalTestCase;

final class EventStoreControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.event_store.index', ['filter' => ['event_store.sourceServiceName' => 'test']])
        );

        $this->assertResponseIsSuccessful();
        $response = $this->getControllerResponse();
        $this->assertResponseEnvelope($response);

        $result = $response['results'][0];
        self::assertArrayHasKeys(
            ['id', 'messageId', 'messageName', 'sourceServiceName', 'payload', 'createdAt'],
            $result
        );

        self::assertIsString($result['id']);
        self::assertIsString($result['messageId']);
        self::assertIsString($result['messageName']);
        self::assertIsString($result['sourceServiceName']);
        self::assertIsArray($result['payload']);
        self::assertIsString($result['createdAt']);

        self::assertArrayHasKey('title', $result['payload']);
        self::assertIsString($result['payload']['title']);
    }
}
