<?php

namespace App\Tests\Unit\Service\PickList\BugReport;

use App\Entity\Inventory;
use App\Entity\PickList;
use App\Entity\PickListBugReport;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\Warehouse;
use App\Service\PickList\BugReport\PickListBugReportFactory;
use App\Service\PickList\BugReport\PickListBugReportService;
use App\Service\PickList\PickListService;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class PickListBugReportServiceTest extends MockeryTestCase
{
    public function testCreate(): void
    {
        $pickList        = Mockery::mock(PickList::class);
        $manager         = Mockery::mock(EntityManagerInterface::class);
        $factory         = Mockery::mock(PickListBugReportFactory::class);
        $pickListService = Mockery::mock(PickListService::class);
        $receiptItem     = Mockery::mock(ReceiptItem::class);
        $receipt         = Mockery::mock(Receipt::class);

        $pickList->shouldReceive('getPickListBugReport')
                 ->once()
                 ->withNoArgs()
                 ->andReturnNull();

        $pickList->shouldReceive('getRemainedQuantity')
                 ->once()
                 ->withNoArgs()
                 ->andReturn(1);

        $pickList->shouldReceive('getReceiptItem')
                 ->times(3)
                 ->withNoArgs()
                 ->andReturn($receiptItem);

        $receiptItem->shouldReceive('getReceipt')
                    ->once()
                    ->withNoArgs()
                    ->andReturn($receipt);

        $receipt->shouldReceive('getSourceWarehouse')
            ->once()
            ->withNoArgs()
            ->andReturn(new Warehouse());

        $receiptItem->shouldReceive('getInventory')
                    ->once()
                    ->withNoArgs()
                    ->andReturn(new Inventory());

        $pickListService->shouldReceive('create')
                        ->once()
                        ->with(Mockery::type(ReceiptItem::class))
                        ->andReturn([]);

        $factory->shouldReceive('create')
                ->once()
                ->withNoArgs()
                ->andReturn(new PickListBugReport());

        $manager->shouldReceive('persist')
                ->once()
                ->with(Mockery::type(PickListBugReport::class))
                ->andReturn();

        $manager->shouldReceive('flush')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $manager->shouldReceive('beginTransaction')
            ->once()
            ->withNoArgs()
            ->andReturn();

        $manager->shouldReceive('commit')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $pickListBugReportService = new PickListBugReportService($manager, $factory, $pickListService);
        self::assertInstanceOf(PickListBugReport::class, $pickListBugReportService->create($pickList));
    }
}
