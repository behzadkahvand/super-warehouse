<?php

namespace App\Tests\Unit\Service\ItemBatch;

use App\Entity\ItemBatch;
use App\Entity\ReceiptItem;
use App\Entity\ReceiptItemBatch;
use App\Events\ItemBatch\ItemBatchCreatedEvent;
use App\Service\ItemBatch\ItemBatchService;
use App\Service\ItemBatch\ReceiptItemBatchFactory;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ItemBatchServiceTest extends MockeryTestCase
{
    public function testStore(): void
    {
        $manager                 = Mockery::mock(EntityManagerInterface::class);
        $dispatcher              = Mockery::mock(EventDispatcherInterface::class);
        $receiptItemBatchFactory = Mockery::mock(ReceiptItemBatchFactory::class);
        $itemBatch               = new ItemBatch();
        $reciptItem              = new ReceiptItem();
        $receiptItemBatch        = new ReceiptItemBatch();

        $manager->shouldReceive('persist')
                ->once()
                ->with($itemBatch)
                ->andReturn();

        $manager->shouldReceive('flush')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $dispatcher->shouldReceive('dispatch')
                   ->once()
                   ->with(Mockery::type(ItemBatchCreatedEvent::class))
                   ->andReturn(new stdClass());

        $receiptItemBatchFactory->shouldReceive('create')
                                ->once()
                                ->withNoArgs()
                                ->andReturn($receiptItemBatch);

        $itemBatchService = new ItemBatchService($manager, $dispatcher, $receiptItemBatchFactory);

        self::assertInstanceOf(ItemBatch::class, $itemBatchService->store($itemBatch, $reciptItem));
    }
}
