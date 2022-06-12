<?php

namespace App\Tests\Unit\Service\PickList;

use App\Dictionary\WarehouseTrackingTypeDictionary;
use App\Entity\Inventory;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\Warehouse;
use App\Service\PickList\PickListFactory;
use App\Service\PickList\PickListService;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class PickListServiceTest extends MockeryTestCase
{
    public function testCreate(): void
    {
        $receiptItem       = \Mockery::mock(ReceiptItem::class);
        $receipt           = \Mockery::mock(Receipt::class);
        $warehouse           = \Mockery::mock(Warehouse::class);
        $manager           = \Mockery::mock(EntityManagerInterface::class);
        $itemSerialFilters = [];

        $receiptItem->shouldReceive('getReceipt')
                    ->once()
                    ->withNoArgs()
                    ->andReturn($receipt);

        $receiptItem->shouldReceive('getRemainedQuantity')
                    ->once()
                    ->withNoArgs()
                    ->andReturn(1);

        $receiptItem->shouldReceive('getInventory')
                    ->once()
                    ->withNoArgs()
                    ->andReturn(new Inventory());

        $receipt->shouldReceive('getSourceWarehouse')
                ->once()
                ->withNoArgs()
                ->andReturn($warehouse);

        $warehouse->shouldReceive('getTrackingType')
            ->once()
            ->withNoArgs()
            ->andReturn(WarehouseTrackingTypeDictionary::SERIAL);

        $manager->shouldReceive('flush')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $pickListService = new PickListService($itemSerialFilters, new PickListFactory(), $manager);
        $pickListService->create($receiptItem);
    }
}
