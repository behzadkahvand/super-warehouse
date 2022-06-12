<?php

namespace App\Tests\Unit\Service\Relocate\Picking;

use App\Entity\Admin;
use App\Entity\Inventory;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Repository\PickListRepository;
use App\Service\Relocate\Exceptions\BinRelocateReserveStockLimitException;
use App\Service\Relocate\Exceptions\ItemNotInStorageBinException;
use App\Service\Relocate\Exceptions\ItemRelocateReserveStockLimitException;
use App\Service\Relocate\Picking\RelocatePickingService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Security\Core\Security;

class RelocatePickingServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|Mockery\MockInterface|Security|null $security;

    protected CacheItemPoolInterface|Mockery\LegacyMockInterface|Mockery\MockInterface|null $cacheItemPoolInterface;

    protected Mockery\LegacyMockInterface|PickListRepository|Mockery\MockInterface|null $pickListRepository;

    protected Mockery\LegacyMockInterface|ItemSerialRepository|Mockery\MockInterface|null $itemSerialRepository;

    protected RelocatePickingService|null $relocatePickingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->security               = Mockery::mock(Security::class);
        $this->cacheItemPoolInterface = Mockery::mock(CacheItemPoolInterface::class);
        $this->pickListRepository     = Mockery::mock(PickListRepository::class);
        $this->itemSerialRepository   = Mockery::mock(ItemSerialRepository::class);

        $this->relocatePickingService = new RelocatePickingService(
            $this->security,
            $this->cacheItemPoolInterface,
            $this->pickListRepository,
            $this->itemSerialRepository
        );
    }

    public function testItCanRelocateBin(): void
    {
        $bin = Mockery::mock(WarehouseStorageBin::class);

        $this->pickListRepository->expects('getStorageBinReserveStocksCount')
                                 ->with($bin)
                                 ->andReturnNull();

        $this->relocatePickingService->checkCanRelocateBin($bin);
    }

    public function testItCanNotRelocateBin(): void
    {
        $bin = Mockery::mock(WarehouseStorageBin::class);

        $this->pickListRepository->expects('getStorageBinReserveStocksCount')
                                 ->with($bin)
                                 ->andReturn(5);

        self::expectException(BinRelocateReserveStockLimitException::class);

        $this->relocatePickingService->checkCanRelocateBin($bin);
    }

    public function testItCanRelocateItem(): void
    {
        $bin = Mockery::mock(WarehouseStorageBin::class);
        $bin->shouldReceive('getId')
            ->times(3)
            ->withNoArgs()
            ->andReturn(1);

        $itemSerial = Mockery::mock(ItemSerial::class);
        $itemSerial->shouldReceive('getWarehouseStorageBin')
                   ->once()
                   ->withNoArgs()
                   ->andReturn($bin);

        $inventory = Mockery::mock(Inventory::class);
        $itemSerial->shouldReceive('getInventory')
                   ->once()
                   ->withNoArgs()
                   ->andReturn($inventory);

        $inventory->expects('getId')
            ->withNoArgs()
            ->andReturn(1);

        $admin = Mockery::mock(Admin::class);
        $this->security->expects('getUser')
                       ->withNoArgs()
                       ->andReturn($admin);

        $admin->expects('getId')->withNoArgs()->andReturn(1);

        $cacheItem = Mockery::mock(CacheItemInterface::class);
        $this->cacheItemPoolInterface->expects('getItem')
                                     ->andReturn($cacheItem);

        $cacheItem->expects('isHit')
            ->withNoArgs()
            ->andReturnTrue();

        $cacheItem->expects('get')
                  ->withNoArgs()
                  ->andReturn(2);

        $this->itemSerialRepository->expects('getItemSerialsCountByInventoryInSpecificBin')
                                   ->with($inventory, $bin)
                                   ->andReturn(10);

        $this->pickListRepository->expects('getReserveStocksCountForInventoryInSpecificBin')
                                 ->with($inventory, $bin)
                                 ->andReturn(3);

        $cacheItem->expects('set')
                  ->with(3)
                  ->andReturnSelf();

        $cacheItem->expects('expiresAfter')
                  ->with(3 * 60 * 60)
                  ->andReturnSelf();

        $this->cacheItemPoolInterface->expects('save')
            ->with($cacheItem)
            ->andReturnTrue();

        $this->relocatePickingService->checkCanRelocateItem($bin, $itemSerial);
    }

    public function testRelocateItemExceptionWhenItemNotInStorageBin(): void
    {
        $bin = Mockery::mock(WarehouseStorageBin::class);
        $bin->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn(1);

        $bin2 = Mockery::mock(WarehouseStorageBin::class);
        $bin2->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn(2);

        $itemSerial = Mockery::mock(ItemSerial::class);
        $itemSerial->shouldReceive('getWarehouseStorageBin')
                   ->once()
                   ->withNoArgs()
                   ->andReturn($bin2);

        self::expectException(ItemNotInStorageBinException::class);

        $this->relocatePickingService->checkCanRelocateItem($bin, $itemSerial);
    }

    public function testRelocateItemExceptionWhenItemIsReservedStock(): void
    {
        $bin = Mockery::mock(WarehouseStorageBin::class);
        $bin->shouldReceive('getId')
            ->times(3)
            ->withNoArgs()
            ->andReturn(1);

        $itemSerial = Mockery::mock(ItemSerial::class);
        $itemSerial->shouldReceive('getWarehouseStorageBin')
                   ->once()
                   ->withNoArgs()
                   ->andReturn($bin);

        $inventory = Mockery::mock(Inventory::class);
        $itemSerial->shouldReceive('getInventory')
                   ->once()
                   ->withNoArgs()
                   ->andReturn($inventory);

        $inventory->expects('getId')
                  ->withNoArgs()
                  ->andReturn(1);

        $admin = Mockery::mock(Admin::class);
        $this->security->expects('getUser')
                       ->withNoArgs()
                       ->andReturn($admin);

        $admin->expects('getId')->withNoArgs()->andReturn(1);

        $cacheItem = Mockery::mock(CacheItemInterface::class);
        $this->cacheItemPoolInterface->expects('getItem')
                                     ->andReturn($cacheItem);

        $cacheItem->expects('isHit')
                  ->withNoArgs()
                  ->andReturnTrue();

        $cacheItem->expects('get')
                  ->withNoArgs()
                  ->andReturn(8);

        $this->itemSerialRepository->expects('getItemSerialsCountByInventoryInSpecificBin')
                                   ->with($inventory, $bin)
                                   ->andReturn(10);

        $this->pickListRepository->expects('getReserveStocksCountForInventoryInSpecificBin')
                                 ->with($inventory, $bin)
                                 ->andReturn(3);

        self::expectException(ItemRelocateReserveStockLimitException::class);

        $this->relocatePickingService->checkCanRelocateItem($bin, $itemSerial);
    }
}
