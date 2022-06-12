<?php

namespace App\Tests\Unit\Service\Inventory;

use App\Entity\Inventory;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Inventory\DTO\InventoryData;
use App\Service\Inventory\InventoryCreateService;
use App\Service\Inventory\InventoryFactory;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class InventoryCreateServiceTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $em;

    protected ProductRepository|LegacyMockInterface|MockInterface|null $productRepository;

    protected InventoryFactory|LegacyMockInterface|MockInterface|null $factoryMock;

    protected Inventory|LegacyMockInterface|MockInterface|null $inventoryMock;

    protected InventoryData|LegacyMockInterface|MockInterface|null $dataMock;

    protected ?InventoryCreateService $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em                = Mockery::mock(EntityManagerInterface::class);
        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->factoryMock       = Mockery::mock(InventoryFactory::class);
        $this->inventoryMock     = Mockery::mock(Inventory::class);
        $this->dataMock          = Mockery::mock(InventoryData::class);

        $this->sut = new InventoryCreateService(
            $this->em,
            $this->productRepository,
            $this->factoryMock
        );
    }

    public function testItCanCreateInventory(): void
    {
        $this->dataMock->expects('getProductId')->withNoArgs()->andReturns(1);

        $product = Mockery::mock(Product::class);
        $this->productRepository->expects('find')->with(1)->andReturns($product);

        $this->factoryMock->expects('create')->withNoArgs()->andReturns($this->inventoryMock);

        $this->dataMock->expects('getId')->withNoArgs()->andReturns(1);
        $this->dataMock->expects('getGuarantee')->withNoArgs()->andReturns('iran guarantee');
        $this->dataMock->expects('getSize')->withNoArgs()->andReturnNull();
        $this->dataMock->expects('getColor')->withNoArgs()->andReturns('Green');

        $this->inventoryMock->expects('setId')->with(1)->andReturnSelf();
        $this->inventoryMock->expects('setGuarantee')->with('iran guarantee')->andReturnSelf();
        $this->inventoryMock->expects('setSize')->with(null)->andReturnSelf();
        $this->inventoryMock->expects('setColor')->with('Green')->andReturnSelf();
        $this->inventoryMock->expects('setProduct')->with($product)->andReturnSelf();

        $this->em->expects('persist')->with($this->inventoryMock)->andReturns();
        $this->em->expects('flush')->withNoArgs()->andReturns();

        $this->sut->create($this->dataMock);
    }

    public function testItCanNotUpdateInventoryWhenProductNotFound(): void
    {
        $this->dataMock->expects('getProductId')->withNoArgs()->andReturns(1);

        $this->productRepository->expects('find')->with(1)->andReturnNull();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('product of inventory not found!');

        $this->sut->create($this->dataMock);
    }
}
