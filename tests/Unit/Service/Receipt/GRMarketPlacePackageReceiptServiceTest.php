<?php

namespace App\Tests\Unit\Service\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\DTO\GRMarketPlacePackageReceiptData;
use App\Entity\Inventory;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\ReceiptItem;
use App\Entity\SellerPackage;
use App\Entity\SellerPackageItem;
use App\Entity\Warehouse;
use App\Events\Receipt\GRMarketPlacePackageCreatedEvent;
use App\Service\Receipt\Exceptions\ReceiptHasNoItemException;
use App\Service\Receipt\GRMarketPlacePackageReceiptService;
use App\Service\Receipt\ReceiptFactory;
use App\Service\Receipt\ReceiptItemFactory;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GRMarketPlacePackageReceiptServiceTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $managerMock;

    protected ReceiptFactory|LegacyMockInterface|MockInterface|null $receiptFactoryMock;

    protected LegacyMockInterface|ReceiptItemFactory|MockInterface|null $receiptItemFactoryMock;

    protected LegacyMockInterface|EventDispatcherInterface|MockInterface|null $dispatcherMock;

    protected LegacyMockInterface|SellerPackage|MockInterface|null $sellerPackageMock;

    protected SellerPackageItem|LegacyMockInterface|MockInterface|null $sellerPackageItemMock;

    protected LegacyMockInterface|Warehouse|MockInterface|null $warehouseMock;

    protected Inventory|LegacyMockInterface|MockInterface|null $inventoryMock;

    protected GRMarketPlacePackageReceipt|LegacyMockInterface|MockInterface|null $receiptMock;

    protected LegacyMockInterface|ReceiptItem|MockInterface|null $receiptItemMock;

    protected LegacyMockInterface|GRMarketPlacePackageReceiptData|MockInterface|null $receiptDataMock;

    protected ?GRMarketPlacePackageReceiptService $receiptService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->managerMock            = Mockery::mock(EntityManagerInterface::class);
        $this->receiptFactoryMock     = Mockery::mock(ReceiptFactory::class);
        $this->receiptItemFactoryMock = Mockery::mock(ReceiptItemFactory::class);
        $this->dispatcherMock         = Mockery::mock(EventDispatcherInterface::class);
        $this->sellerPackageMock      = Mockery::mock(SellerPackage::class);
        $this->sellerPackageItemMock  = Mockery::mock(SellerPackageItem::class);
        $this->warehouseMock          = Mockery::mock(Warehouse::class);
        $this->inventoryMock          = Mockery::mock(Inventory::class);
        $this->receiptMock            = Mockery::mock(GRMarketPlacePackageReceipt::class);
        $this->receiptItemMock        = Mockery::mock(ReceiptItem::class);
        $this->receiptDataMock        = Mockery::mock(GRMarketPlacePackageReceiptData::class);

        $this->receiptService = new GRMarketPlacePackageReceiptService(
            $this->managerMock,
            $this->receiptItemFactoryMock,
            $this->dispatcherMock,
            $this->receiptFactoryMock
        );
    }

    public function testItCanMakeGoodReceipt(): void
    {
        $this->receiptDataMock->shouldReceive('getWarehouse')
                              ->once()
                              ->withNoArgs()
                              ->andReturn($this->warehouseMock);

        $this->receiptDataMock->shouldReceive('getSellerPackage')
                              ->once()
                              ->withNoArgs()
                              ->andReturn($this->sellerPackageMock);

        $this->receiptMock->shouldReceive('setStatus')
                          ->once()
                          ->with(ReceiptStatusDictionary::APPROVED)
                          ->andReturnSelf();

        $this->receiptMock->shouldReceive('setSourceWarehouse')
                          ->once()
                          ->with($this->warehouseMock)
                          ->andReturnSelf();

        $this->receiptMock->shouldReceive('setReference')
                          ->once()
                          ->with($this->sellerPackageMock)
                          ->andReturnSelf();

        $this->receiptMock->shouldReceive('getReference')
                          ->once()
                          ->withNoArgs()
                          ->andReturn($this->sellerPackageMock);

        $this->receiptItemMock->shouldReceive('setReceipt')
                              ->once()
                              ->with($this->receiptMock)
                              ->andReturnSelf();

        $this->receiptItemMock->shouldReceive('setStatus')
                              ->once()
                              ->with(ReceiptStatusDictionary::APPROVED)
                              ->andReturnSelf();

        $this->receiptItemMock->shouldReceive('setQuantity')
                              ->once()
                              ->with(2)
                              ->andReturnSelf();

        $this->sellerPackageItemMock->shouldReceive('hasActual')
                                    ->once()
                                    ->withNoArgs()
                                    ->andReturnTrue();

        $this->sellerPackageItemMock->shouldReceive('getActualQuantity')
                                    ->once()
                                    ->withNoArgs()
                                    ->andReturn(2);

        $this->sellerPackageItemMock->shouldReceive('getInventory')
                                    ->once()
                                    ->withNoArgs()
                                    ->andReturn($this->inventoryMock);

        $this->receiptItemMock->shouldReceive('setInventory')
                              ->once()
                              ->with($this->inventoryMock)
                              ->andReturnSelf();

        $this->sellerPackageMock->shouldReceive('getPackageItems')
                                ->once()
                                ->withNoArgs()
                                ->andReturn(new ArrayCollection([$this->sellerPackageItemMock]));

        $this->receiptFactoryMock->shouldReceive('create')
                                 ->once()
                                 ->with(ReceiptReferenceTypeDictionary::GR_MP_PACKAGE)
                                 ->andReturn($this->receiptMock);

        $this->receiptItemFactoryMock->shouldReceive('create')
                                     ->once()
                                     ->withNoArgs()
                                     ->andReturn($this->receiptItemMock);

        $this->dispatcherMock->shouldReceive('dispatch')
                             ->once()
                             ->with(Mockery::type(GRMarketPlacePackageCreatedEvent::class))
                             ->andReturn(new stdClass());

        $this->managerMock->shouldReceive('persist')
                          ->once()
                          ->with($this->receiptMock)
                          ->andReturn();

        $this->managerMock->shouldReceive('persist')
                          ->once()
                          ->with($this->receiptItemMock)
                          ->andReturn();

        $this->managerMock->shouldReceive('flush')
                          ->once()
                          ->withNoArgs()
                          ->andReturn();

        $result = $this->receiptService->makeReceipt($this->receiptDataMock);

        self::assertInstanceOf(GRMarketPlacePackageReceipt::class, $result);
    }

    public function testItHasAnExceptionOnMakingReceiptWhenReceiptHasNoItems(): void
    {
        $this->receiptDataMock->shouldReceive('getWarehouse')
                              ->once()
                              ->withNoArgs()
                              ->andReturn($this->warehouseMock);

        $this->receiptDataMock->shouldReceive('getSellerPackage')
                              ->once()
                              ->withNoArgs()
                              ->andReturn($this->sellerPackageMock);

        $this->receiptMock->shouldReceive('setStatus')
                          ->once()
                          ->with(ReceiptStatusDictionary::APPROVED)
                          ->andReturnSelf();

        $this->receiptMock->shouldReceive('setSourceWarehouse')
                          ->once()
                          ->with($this->warehouseMock)
                          ->andReturnSelf();

        $this->receiptMock->shouldReceive('setReference')
                          ->once()
                          ->with($this->sellerPackageMock)
                          ->andReturnSelf();

        $this->receiptMock->shouldReceive('getReference')
                          ->once()
                          ->withNoArgs()
                          ->andReturn($this->sellerPackageMock);

        $this->sellerPackageItemMock->shouldReceive('hasActual')
                                    ->once()
                                    ->withNoArgs()
                                    ->andReturnFalse();

        $this->sellerPackageMock->shouldReceive('getPackageItems')
                                ->once()
                                ->withNoArgs()
                                ->andReturn(new ArrayCollection([$this->sellerPackageItemMock]));

        $this->receiptFactoryMock->shouldReceive('create')
                                 ->once()
                                 ->with(ReceiptReferenceTypeDictionary::GR_MP_PACKAGE)
                                 ->andReturn($this->receiptMock);

        $this->managerMock->shouldReceive('persist')
                          ->once()
                          ->with($this->receiptMock)
                          ->andReturn();

        self::expectException(ReceiptHasNoItemException::class);
        self::expectExceptionCode(422);
        self::expectExceptionMessage('The receipt must have at least one item!');

        $this->receiptService->makeReceipt($this->receiptDataMock);
    }
}
