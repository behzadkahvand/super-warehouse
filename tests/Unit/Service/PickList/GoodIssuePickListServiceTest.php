<?php

namespace App\Tests\Unit\Service\PickList;

use App\Entity\PickList;
use App\Entity\Receipt\STOutboundReceipt;
use App\Entity\ReceiptItem;
use App\Entity\Warehouse;
use App\Events\PickList\PickListCreatedEventInterface;
use App\Service\PickList\PickListFactory;
use App\Service\PickList\GoodIssuePickListService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class GoodIssuePickListServiceTest extends MockeryTestCase
{
    public function testCreate(): void
    {
        $pickListFactory = Mockery::mock(PickListFactory::class);
        $manager         = Mockery::mock(EntityManagerInterface::class);
        $dispatcher      = Mockery::mock(EventDispatcherInterface::class);
        $receipt         = Mockery::mock(STOutboundReceipt::class);
        $receiptItem     = Mockery::mock(ReceiptItem::class);

        $receipt->shouldReceive('getReceiptItems')
                ->once()
                ->withNoArgs()
                ->andReturn(new ArrayCollection([$receiptItem]));

        $receipt->shouldReceive('getSourceWarehouse')
                ->once()
                ->withNoArgs()
                ->andReturn(new Warehouse());

        $receiptItem->shouldReceive('getQuantity')
                    ->once()
                    ->withNoArgs()
                    ->andReturn(1);

        $pickListFactory->shouldReceive('create')
                        ->once()
                        ->withNoArgs()
                        ->andReturn(new PickList());

        $manager->shouldReceive('persist')
                ->once()
                ->with(Mockery::type(PickList::class))
                ->andReturn();

        $manager->shouldReceive('flush')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $dispatcher->shouldReceive('dispatch')
                   ->once()
                   ->with(Mockery::type(PickListCreatedEventInterface::class))
                   ->andReturn(new stdClass());

        $pickListService = new GoodIssuePickListService(
            $pickListFactory,
            $manager,
            $dispatcher
        );

        $pickListService->create($receipt);
    }
}
