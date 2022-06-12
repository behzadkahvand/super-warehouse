<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Admin;
use App\Tests\Functional\FunctionalTestCase;

class AdminControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.admin.index')
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'name',
            'family',
            'mobile',
            'email',
            'isActive',
            'createdAt',
            'updatedAt',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['name']);
        self::assertIsString($item['family']);
        self::assertIsString($item['mobile']);
        self::assertIsString($item['email']);
        self::assertIsBool($item['isActive']);
        self::assertIsInt(strtotime($item['createdAt']));
        self::assertIsInt(strtotime($item['updatedAt']));
    }

    public function testShow(): void
    {
        $admin = $this->manager->getRepository(Admin::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.admin.show', ['id' => $admin->getId()])
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'name',
            'family',
            'mobile',
            'email',
            'isActive',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['name']);
        self::assertIsString($item['family']);
        self::assertIsString($item['mobile']);
        self::assertIsString($item['email']);
        self::assertIsBool($item['isActive']);
    }

    public function testStore(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.admin.store'),
            [
                'name'     => 'test',
                'family'   => 'test',
                'mobile'   => '09129876543',
                'email'    => 'test@test.ir',
                'isActive' => true,
                'password' => 'test',
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'name',
            'family',
            'mobile',
            'email',
            'isActive',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['name']);
        self::assertIsString($item['family']);
        self::assertIsString($item['mobile']);
        self::assertIsString($item['email']);
        self::assertIsBool($item['isActive']);
    }

    public function testStoreValidationError(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'POST',
            $this->route('admin.admin.store'),
            [
                'name'     => 'test',
                'family'   => 'test',
                'mobile'   => '09129876543',
                'email'    => 'test',
                'isActive' => true,
                'password' => 'test',
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKey('email', $item);
        self::assertEquals('This value is not a valid email address.', $item['email'][0]);
    }

    public function testUpdate(): void
    {
        $admin = $this->manager->getRepository(Admin::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.admin.update', ['id' => $admin->getId()]),
            [
                'name' => 'test2',
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'name',
            'family',
            'mobile',
            'email',
            'isActive',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['name']);
        self::assertIsString($item['family']);
        self::assertIsString($item['mobile']);
        self::assertIsString($item['email']);
        self::assertIsBool($item['isActive']);
    }
}
