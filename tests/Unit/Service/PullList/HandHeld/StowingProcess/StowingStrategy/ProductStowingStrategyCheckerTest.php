<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\StowingStrategy;

use App\Entity\Inventory;
use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\Product;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\ProductStowingStrategyException;
use App\Service\PullList\HandHeld\StowingProcess\StowingStrategy\ProductStowingStrategyChecker;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class ProductStowingStrategyCheckerTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|ItemSerialRepository|Mockery\MockInterface|null $itemSerialRepository;

    protected ProductStowingStrategyChecker|null $checker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemSerialRepository = Mockery::mock(ItemSerialRepository::class);

        $this->checker = new ProductStowingStrategyChecker($this->itemSerialRepository);
    }

    public function testSupport(): void
    {
        $storageArea = Mockery::mock(WarehouseStorageArea::class);
        $storageArea->expects('getStowingStrategy')
                    ->withNoArgs()
                    ->andReturn("PRODUCT");
        self::assertTrue($this->checker->support($storageArea));
    }

    public function testCheck(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);
        $storageBin = Mockery::mock(WarehouseStorageBin::class);

        $itemBatch = Mockery::mock(ItemBatch::class);
        $itemSerial->expects('getItemBatch')
                   ->withNoArgs()
                   ->andReturn($itemBatch);

        $inventory = Mockery::mock(Inventory::class);

        $product = Mockery::mock(Product::class);
        $inventory->expects('getProduct')
                  ->withNoArgs()
                  ->andReturn($product);

        $itemSerial->expects('getInventory')
                   ->withNoArgs()
                   ->andReturn($inventory);

        $this->itemSerialRepository->expects('getSameProductItemsCountWithDifferentBatchInSpecificBin')
                                   ->with($storageBin, $itemBatch, $product)
                                   ->andReturn(1);

        self::expectException(ProductStowingStrategyException::class);

        $this->checker->check($storageBin, $itemSerial);
    }
}
