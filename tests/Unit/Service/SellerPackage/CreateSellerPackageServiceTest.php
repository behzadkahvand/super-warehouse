<?php

namespace App\Tests\Unit\Service\SellerPackage;

use App\Dictionary\SellerPackageTypeDictionary;
use App\Dictionary\SellerPackageStatusDictionary;
use App\Dictionary\SellerPackageProductTypeDictionary;
use App\Entity\Inventory;
use App\Entity\Seller;
use App\Entity\SellerPackage;
use App\Entity\SellerPackageItem;
use App\Entity\Warehouse;
use App\Repository\InventoryRepository;
use App\Repository\SellerRepository;
use App\Repository\WarehouseRepository;
use App\Service\SellerPackage\CreateSellerPackageService;
use App\Service\SellerPackage\DTO\CreateSellerPackageData;
use App\Service\SellerPackage\SellerPackageFactory;
use App\Tests\Unit\BaseUnitTestCase;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class CreateSellerPackageServiceTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $managerMock;

    protected SellerPackageFactory|LegacyMockInterface|MockInterface|null $factoryMock;

    protected SellerRepository|LegacyMockInterface|MockInterface|null $sellerRepoMock;

    protected WarehouseRepository|LegacyMockInterface|MockInterface|null $warehouseRepoMock;

    protected InventoryRepository|LegacyMockInterface|MockInterface|null $inventoryRepoMock;

    protected LegacyMockInterface|SellerPackage|MockInterface|null $sellerPackageMock;

    protected SellerPackageItem|LegacyMockInterface|MockInterface|null $sellerPackageItemMock;

    protected Seller|LegacyMockInterface|MockInterface|null $sellerMock;

    protected LegacyMockInterface|Warehouse|MockInterface|null $warehouseMock;

    protected Inventory|LegacyMockInterface|MockInterface|null $inventoryMock;

    protected ?CreateSellerPackageData $createData;

    protected ?CreateSellerPackageService $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->managerMock           = Mockery::mock(EntityManagerInterface::class);
        $this->factoryMock           = Mockery::mock(SellerPackageFactory::class);
        $this->sellerRepoMock        = Mockery::mock(SellerRepository::class);
        $this->warehouseRepoMock     = Mockery::mock(WarehouseRepository::class);
        $this->inventoryRepoMock     = Mockery::mock(InventoryRepository::class);
        $this->sellerPackageMock     = Mockery::mock(SellerPackage::class);
        $this->sellerPackageItemMock = Mockery::mock(SellerPackageItem::class);
        $this->sellerMock            = Mockery::mock(Seller::class);
        $this->warehouseMock         = Mockery::mock(Warehouse::class);
        $this->inventoryMock         = Mockery::mock(Inventory::class);

        $this->createData = new CreateSellerPackageData([
            'id'          => 2,
            'status'      => SellerPackageStatusDictionary::SENT,
            'productType' => SellerPackageProductTypeDictionary::NON_FMCG,
            'packageType' => SellerPackageTypeDictionary::DEPOT,
            'sellerId'    => 1,
            'warehouseId' => 2,
            'createdAt'   => new DateTime(),
            'items'       => [
                [
                    'id'          => 3,
                    'inventoryId' => 1523,
                    'status'      => SellerPackageStatusDictionary::SENT,
                    'quantity'    => 5,
                ],
                [
                    'id'          => 4,
                    'inventoryId' => 785,
                    'status'      => SellerPackageStatusDictionary::SENT,
                    'quantity'    => 8,
                ],
            ],
        ]);

        $this->sut = new CreateSellerPackageService(
            $this->managerMock,
            $this->factoryMock,
            $this->sellerRepoMock,
            $this->warehouseRepoMock,
            $this->inventoryRepoMock
        );
    }

    public function testItHasAnExceptionWhenWarehouseNotFound(): void
    {
        $this->warehouseRepoMock->expects('find')
                                ->with(2)
                                ->andReturnNull();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Warehouse not found!');

        ($this->sut)($this->createData);
    }

    public function testItHasAnExceptionWhenSellerNotFound(): void
    {
        $this->warehouseRepoMock->expects('find')
                                ->with(2)
                                ->andReturns($this->warehouseMock);

        $this->sellerRepoMock->expects('find')
                             ->with(1)
                             ->andReturnNull();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Seller not found!');

        ($this->sut)($this->createData);
    }

    public function testItHasAnExceptionWhenInventoryNotFound(): void
    {
        $this->warehouseRepoMock->expects('find')
                                ->with(2)
                                ->andReturns($this->warehouseMock);

        $this->sellerRepoMock->expects('find')
                             ->with(1)
                             ->andReturns($this->sellerMock);

        $this->factoryMock->expects('getSellerPackage')
                          ->withNoArgs()
                          ->andReturns($this->sellerPackageMock);

        $this->sellerPackageMock->expects('setId')
                                ->with(2)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setPackageType')
                                ->with(SellerPackageTypeDictionary::DEPOT)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setProductType')
                                ->with(SellerPackageProductTypeDictionary::NON_FMCG)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setStatus')
                                ->with(SellerPackageStatusDictionary::SENT)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setWarehouse')
                                ->with($this->warehouseMock)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setSeller')
                                ->with($this->sellerMock)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setCreatedAt')
                                ->with(Mockery::type(DateTimeInterface::class))
                                ->andReturnSelf();

        $this->inventoryRepoMock->expects('find')
                                ->with(1523)
                                ->andReturnNull();

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Inventory not found!');

        ($this->sut)($this->createData);
    }

    public function testItCanCreateSellerPackageWithItems(): void
    {
        $this->warehouseRepoMock->expects('find')
                                ->with(2)
                                ->andReturns($this->warehouseMock);

        $this->sellerRepoMock->expects('find')
                             ->with(1)
                             ->andReturns($this->sellerMock);

        $this->factoryMock->expects('getSellerPackage')
                          ->withNoArgs()
                          ->andReturns($this->sellerPackageMock);
        $this->factoryMock->expects('getSellerPackageItem')
                          ->twice()
                          ->withNoArgs()
                          ->andReturns($this->sellerPackageItemMock);

        $this->sellerPackageMock->expects('setId')
                                ->with(2)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setPackageType')
                                ->with(SellerPackageTypeDictionary::DEPOT)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setProductType')
                                ->with(SellerPackageProductTypeDictionary::NON_FMCG)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setStatus')
                                ->with(SellerPackageStatusDictionary::SENT)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setWarehouse')
                                ->with($this->warehouseMock)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setSeller')
                                ->with($this->sellerMock)
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('setCreatedAt')
                                ->with(Mockery::type(DateTimeInterface::class))
                                ->andReturnSelf();
        $this->sellerPackageMock->expects('addPackageItem')
                                ->twice()
                                ->with($this->sellerPackageItemMock)
                                ->andReturnSelf();

        $this->inventoryRepoMock->expects('find')
                                ->with(1523)
                                ->andReturns($this->inventoryMock);
        $this->inventoryRepoMock->expects('find')
                                ->with(785)
                                ->andReturns($this->inventoryMock);

        $this->sellerPackageItemMock->expects('setId')
                                    ->with(3)
                                    ->andReturnSelf();
        $this->sellerPackageItemMock->expects('setId')
                                    ->with(4)
                                    ->andReturnSelf();
        $this->sellerPackageItemMock->expects('setStatus')
                                    ->twice()
                                    ->with(SellerPackageStatusDictionary::SENT)
                                    ->andReturnSelf();
        $this->sellerPackageItemMock->expects('setExpectedQuantity')
                                    ->with(5)
                                    ->andReturnSelf();
        $this->sellerPackageItemMock->expects('setExpectedQuantity')
                                    ->with(8)
                                    ->andReturnSelf();
        $this->sellerPackageItemMock->expects('setInventory')
                                    ->twice()
                                    ->with($this->inventoryMock)
                                    ->andReturnSelf();

        $this->managerMock->expects('persist')->with($this->sellerPackageMock)->andReturns();
        $this->managerMock->expects('flush')->withNoArgs()->andReturns();

        ($this->sut)($this->createData);
    }
}
