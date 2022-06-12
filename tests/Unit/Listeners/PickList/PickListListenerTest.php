<?php

namespace App\Tests\Unit\Listeners\PickList;

use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Events\PickList\ShipmentPickListCreatedEvent;
use App\Listeners\PickList\PickListListener;
use App\Service\StatusTransition\StateTransitionHandlerService;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class PickListListenerTest extends MockeryTestCase
{
    public function testUpdateReceiptAndReceiptItems(): void
    {
        $stateTransitionService     = Mockery::mock(StateTransitionHandlerService::class);
        $receipt     = Mockery::mock(Receipt::class);
        $receiptItem = Mockery::mock(ReceiptItem::class);
        $event       = Mockery::mock(ShipmentPickListCreatedEvent::class);

        $event->shouldReceive('getReceipt')
            ->once()
            ->withNoArgs()
            ->andReturn($receipt);

        $receipt->shouldReceive('getReceiptItems')
                ->once()
                ->withNoArgs()
                ->andReturn(new ArrayCollection([$receiptItem]));

        $stateTransitionService->shouldReceive('batchTransitState')
                ->once()
                ->with(Mockery::type('array'), Mockery::type('string'))
                ->andReturn();

        $listener = new PickListListener($stateTransitionService);
        $listener->updateReceiptAndReceiptItems($event);
    }
}
