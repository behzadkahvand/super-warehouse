<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\FunctionalTestCase;

class LocatorControllerTest extends FunctionalTestCase
{
    public function testItCanGetLocatorList(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.locator.index')
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertTrue($response['succeed']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $results = $response['results'][0];

        self::assertArrayHasKeys(['id', 'fullName', 'pullListsCount',], $results);
    }
}
