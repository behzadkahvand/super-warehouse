<?php

namespace App\Tests\Unit\Listeners\ItemBatch;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ItemBatch;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Events\ItemBatch\ItemBatchCreatedEvent;
use App\Listeners\ItemBatch\ItemBatchListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class ItemBatchListenerTest extends MockeryTestCase
{
    public function testUpdateReceiptAndReceiptItemStatus(): void
    {
        $manager     = Mockery::mock(EntityManagerInterface::class);
        $itemBatch   = Mockery::mock(ItemBatch::class);
        $receiptItem = Mockery::mock(ReceiptItem::class);
        $receipt = Mockery::mock(Receipt::class);

        $receiptItem->shouldReceive('getReceipt')
            ->once()
            ->withNoArgs()
            ->andReturn($receipt);

        $receiptItem->shouldReceive('setStatus')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturnSelf();

        $receiptItem->shouldReceive('getStatus')
            ->once()
            ->withNoArgs()
            ->andReturn(ReceiptStatusDictionary::BATCH_PROCESSING);

        $receipt->shouldReceive('getReceiptItems')
            ->once()
            ->withNoArgs()
            ->andReturn(new ArrayCollection([$receiptItem]));

        $receipt->shouldReceive('setStatus')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturnSelf();

        $manager->shouldReceive('flush')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $event    = new ItemBatchCreatedEvent($itemBatch, $receiptItem);

        $listener = new ItemBatchListener($manager);
        $listener->updateReceiptAndReceiptItemStatus($event);
    }
}
