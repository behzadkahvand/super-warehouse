<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\FunctionalTestCase;

class SecurityControllerTest extends FunctionalTestCase
{
    public function testLoginSuccessfully(): void
    {
        $client = $this->sendRequest(
            'POST',
            '/admin/security/login',
            [
                'username' => 'admin@warehouse.com',
                'password' => '123456',
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertArrayHasKeys(['token', 'refreshToken'], $response);
    }
}
