<?php

namespace App\Tests\Unit\Listeners\ItemSerial;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ItemBatch;
use App\Entity\ReceiptItem;
use App\Entity\ReceiptItemBatch;
use App\Entity\ReceiptItemSerial;
use App\Events\ItemSerial\ItemBatchSerialsCreatedEvent;
use App\Listeners\ItemSerial\ItemSerialListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class ItemSerialListenerTest extends MockeryTestCase
{
    public function testUpdateReceiptItemStatus(): void
    {
        $manager     = Mockery::mock(EntityManagerInterface::class);
        $itemBatch   = Mockery::mock(ItemBatch::class);
        $receiptItemBatch = Mockery::mock(ReceiptItemBatch::class);
        $receiptItem = Mockery::mock(ReceiptItem::class);

        $itemBatch->shouldReceive('getReceiptItemBatches')
            ->once()
            ->withNoArgs()
            ->andReturn(new ArrayCollection([$receiptItemBatch]));

        $receiptItemBatch->shouldReceive('getReceiptItem')
            ->once()
            ->withNoArgs()
            ->andReturn($receiptItem);

        $receiptItem->shouldReceive('getReceiptItemSerials')
            ->once()
            ->withNoArgs()
            ->andReturn(new ArrayCollection([new ReceiptItemSerial()]));

        $receiptItem->shouldReceive('getQuantity')
            ->once()
            ->withNoArgs()
            ->andReturn(2);

        $receiptItem->shouldReceive('setStatus')
                    ->once()
                    ->with(Mockery::type('string'))
                    ->andReturnSelf();

        $manager->shouldReceive('flush')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $event    = new ItemBatchSerialsCreatedEvent($itemBatch);

        $listener = new ItemSerialListener($manager);
        $listener->updateReceiptItemStatus($event);
    }
}
