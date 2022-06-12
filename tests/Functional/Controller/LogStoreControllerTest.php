<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\FunctionalTestCase;

final class LogStoreControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.log_store.index', ['filter' => ['log_store.messageId' => 'test']])
        );

        $this->assertResponseIsSuccessful();
        $response = $this->getControllerResponse();
        $this->assertResponseEnvelope($response);

        $result = $response['results'][0];
        self::assertArrayHasKeys(
            ['id', 'messageId', 'status', 'resultCode', 'resultMessage', 'createdAt'],
            $result
        );

        self::assertIsString($result['id']);
        self::assertIsString($result['messageId']);
        self::assertIsString($result['status']);
        self::assertNull($result['resultCode']);
        self::assertNull($result['resultMessage']);
        self::assertIsString($result['createdAt']);
    }
}
