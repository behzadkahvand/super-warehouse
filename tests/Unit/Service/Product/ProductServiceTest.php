<?php

namespace App\Tests\Unit\Service\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Product\DTO\ProductData;
use App\Service\Product\ProductFactory;
use App\Service\Product\ProductService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Mockery;

final class ProductServiceTest extends BaseUnitTestCase
{
    protected ?EntityManagerInterface $manager;

    protected ?ProductRepository $repository;

    protected ?ProductFactory $factory;

    protected ?ProductData $productData;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = Mockery::mock(EntityManagerInterface::class);
        $this->repository = Mockery::mock(ProductRepository::class);
        $this->factory = Mockery::mock(ProductFactory::class);
        $this->productData = Mockery::mock(ProductData::class);

        $this->productData->expects('getId')
            ->withNoArgs()
            ->andReturn(1);
    }

    public function testCreate(): void
    {
        $this->factory->expects('create')
            ->withNoArgs()
            ->andReturn(new Product());

        $this->manager->expects('persist')
            ->with(Mockery::type(Product::class))
            ->andReturn();

        $this->manager->expects('flush')
            ->withNoArgs()
            ->andReturn();

        $this->productData->expects('getTitle')
            ->withNoArgs()
            ->andReturn('test');

        $this->productData->expects('getMainImage')
            ->withNoArgs()
            ->andReturn('default.png');

        $this->productData->expects('getHeight')
            ->withNoArgs()
            ->andReturn(1);

        $this->productData->expects('getWidth')
            ->withNoArgs()
            ->andReturn(1);

        $this->productData->expects('getWeight')
            ->withNoArgs()
            ->andReturn(1);

        $this->productData->expects('getLength')
            ->withNoArgs()
            ->andReturn(1);

        $service = new ProductService($this->manager, $this->repository, $this->factory);
        $service->create($this->productData);
    }

    public function testUpdate(): void
    {
        $this->repository->expects('find')
            ->with(Mockery::type('int'))
            ->andReturn(new Product());

        $this->manager->expects('flush')
            ->withNoArgs()
            ->andReturn();

        $this->productData->expects('getTitle')
            ->withNoArgs()
            ->andReturn('test');

        $this->productData->expects('getMainImage')
            ->withNoArgs()
            ->andReturn('default.png');

        $this->productData->expects('getHeight')
            ->withNoArgs()
            ->andReturn(1);

        $this->productData->expects('getWidth')
            ->withNoArgs()
            ->andReturn(1);

        $this->productData->expects('getWeight')
            ->withNoArgs()
            ->andReturn(1);

        $this->productData->expects('getLength')
            ->withNoArgs()
            ->andReturn(1);

        $service = new ProductService($this->manager, $this->repository, $this->factory);
        $service->update($this->productData);
    }

    public function testUpdateFail(): void
    {
        $this->repository->expects('find')
            ->with(Mockery::type('int'))
            ->andReturnNull();

        self::expectException(EntityNotFoundException::class);

        $service = new ProductService($this->manager, $this->repository, $this->factory);
        $service->update($this->productData);
    }
}
