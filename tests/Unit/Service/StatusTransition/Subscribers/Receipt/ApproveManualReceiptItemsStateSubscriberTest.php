<?php

namespace App\Tests\Unit\Service\StatusTransition\Subscribers\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\StateSubscriberData;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Service\StatusTransition\Subscribers\Receipt\ApproveManualReceiptItemsStateSubscriber;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ApproveManualReceiptItemsStateSubscriberTest extends MockeryTestCase
{
    protected StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $stateTransitionHandlerService;

    protected ApproveManualReceiptItemsStateSubscriber|null $approveManualItemsStatusSubscriber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateTransitionHandlerService      = Mockery::mock(StateTransitionHandlerService::class);
        $this->approveManualItemsStatusSubscriber = new ApproveManualReceiptItemsStateSubscriber(
            $this->stateTransitionHandlerService
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->stateTransitionHandlerService      = null;
        $this->approveManualItemsStatusSubscriber = null;
        Mockery::close();
    }

    public function testItCanCallInvoke(): void
    {
        $receiptItem = Mockery::mock(ReceiptItem::class);
        $items       = new ArrayCollection([$receiptItem]);

        $receipt = Mockery::mock(Receipt::class);

        $receipt->shouldReceive('getStatus')
                ->once()
                ->withNoArgs()
                ->andReturn("APPROVED");

        $receipt->shouldReceive('getReceiptItems')
                ->once()
                ->withNoArgs()
                ->andReturn($items);

        $this->stateTransitionHandlerService->shouldReceive('batchTransitState')
                                            ->once()
                                            ->with($items->toArray(), ReceiptStatusDictionary::APPROVED)
                                            ->andReturn();

        $stateSubscriberData = new StateSubscriberData($receipt, "DRAFT");

        $this->approveManualItemsStatusSubscriber->__invoke($stateSubscriberData);
    }
}
