<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Product;
use App\Tests\Functional\FunctionalTestCase;

class ProductControllerTest extends FunctionalTestCase
{
    public function testIndex(): void
    {
        $this->loginAs($this->admin)->sendRequest(
            'GET',
            $this->route('admin.product.index')
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'][0];

        self::assertArrayHasKeys([
            'id',
            'title',
            'width',
            'height',
            'length',
            'weight',
            'mainImage',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertIsNumeric($item['width']);
        self::assertIsNumeric($item['height']);
        self::assertIsNumeric($item['length']);
        self::assertIsNumeric($item['weight']);
        self::assertIsString($item['mainImage']);
    }

    public function testUpdate(): void
    {
        /** @var Product $product */
        $product = $this->manager->getRepository(Product::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.product.update', ['id' => $product->getId()]),
            [
                'width'  => 200,
                'height' => 200,
                'length' => 200,
                'weight' => 200,
            ]
        );

        self::assertResponseIsSuccessful();

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys([
            'id',
            'title',
            'width',
            'height',
            'length',
            'weight',
            'mainImage',
        ], $item);

        self::assertIsInt($item['id']);
        self::assertIsString($item['title']);
        self::assertIsNumeric($item['width']);
        self::assertIsNumeric($item['height']);
        self::assertIsNumeric($item['length']);
        self::assertIsNumeric($item['weight']);
        self::assertIsString($item['mainImage']);

        self::assertEquals(200, $item['width']);
        self::assertEquals(200, $item['height']);
        self::assertEquals(200, $item['length']);
        self::assertEquals(200, $item['weight']);
    }

    public function testUpdateFail(): void
    {
        /** @var Product $product */
        $product = $this->manager->getRepository(Product::class)->findOneBy([]);
        $this->loginAs($this->admin)->sendRequest(
            'PATCH',
            $this->route('admin.product.update', ['id' => $product->getId()]),
            [
                'title'  => 'test',
                'width'  => null,
                'height' => 200,
                'length' => 200,
                'weight' => 10,
            ]
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertNotEmpty($response['results']);

        $item = $response['results'];

        self::assertArrayHasKeys(['product', 'width'], $item);

        self::assertIsArray($item['product']);
        self::assertIsArray($item['width']);

        self::assertEquals('This form should not contain extra fields.', $item['product'][0]);
        self::assertEquals('This value should not be blank.', $item['width'][0]);
    }
}
