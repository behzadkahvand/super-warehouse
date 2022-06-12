<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\FunctionalTestCase;

class ApiDocHealthCheckTest extends FunctionalTestCase
{
    public function testAdminApiDocIsUpAndRunning(): void
    {
        $response = $this->sendRequest('GET', '/doc');

        self::assertEquals(200, $response->getResponse()->getStatusCode());
    }
}
