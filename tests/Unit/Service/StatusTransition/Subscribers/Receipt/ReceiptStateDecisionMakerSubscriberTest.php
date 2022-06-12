<?php

namespace App\Tests\Unit\Service\StatusTransition\Subscribers\Receipt;

use App\DTO\StateSubscriberData;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Service\StatusTransition\ParentItemStateService;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Service\StatusTransition\Subscribers\Receipt\ReceiptStateDecisionMakerSubscriber;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ReceiptStateDecisionMakerSubscriberTest extends MockeryTestCase
{
    private StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $stateTransitionHandlerService;

    private Mockery\LegacyMockInterface|ParentItemStateService|Mockery\MockInterface|null $parentItemStateService;

    private ReceiptStateDecisionMakerSubscriber|Mockery\MockInterface|null $receiptStateDecisionMakerSubscriber;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateTransitionHandlerService = Mockery::mock(StateTransitionHandlerService::class);
        $this->parentItemStateService        = Mockery::mock(ParentItemStateService::class);

        $this->receiptStateDecisionMakerSubscriber = Mockery::mock(ReceiptStateDecisionMakerSubscriber::class, [
            $this->stateTransitionHandlerService,
            $this->parentItemStateService,
        ])
                                                            ->makePartial()
                                                            ->shouldAllowMockingProtectedMethods();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->parentItemStateService              = null;
        $this->stateTransitionHandlerService       = null;
        $this->receiptStateDecisionMakerSubscriber = null;
        Mockery::close();
    }

    public function testInvokeWhenCurrentAndNextStatusAreEqual(): void
    {
        $receipt = Mockery::mock(Receipt::class);
        $receipt->shouldReceive('getStatus')
                ->once()
                ->withNoArgs()
                ->andReturn("APPROVED");

        $receiptItem = Mockery::mock(ReceiptItem::class);
        $receiptItem->shouldReceive('getReceipt')
                    ->once()
                    ->withNoArgs()
                    ->andReturn($receipt);

        $itemsStatus = ["APPROVED", "READY_TO_PEEK"];
        $this->receiptStateDecisionMakerSubscriber->shouldReceive('getReceiptItemsStatus')
                                                  ->once()
                                                  ->with($receipt)
                                                  ->andReturn($itemsStatus);

        $this->parentItemStateService->shouldReceive('findLowestStatusItems')
                                     ->once()
                                     ->andReturn("APPROVED");

        $stateSubscriberData = new StateSubscriberData($receiptItem, "DRAFT");

        $this->receiptStateDecisionMakerSubscriber->__invoke($stateSubscriberData);
    }

    public function testInvokeWhenCurrentAndNextStatusAreNotEqual(): void
    {
        $receipt = Mockery::mock(Receipt\GRNoneReceipt::class);
        $receipt->shouldReceive('getStatus')
                ->once()
                ->withNoArgs()
                ->andReturn("APPROVED");

        $receiptItem = Mockery::mock(ReceiptItem::class);
        $receiptItem->shouldReceive('getReceipt')
                    ->once()
                    ->withNoArgs()
                    ->andReturn($receipt);

        $nextStatus  = "READY_TO_PEEK";
        $itemsStatus = ["PEEKING", $nextStatus];
        $this->receiptStateDecisionMakerSubscriber->shouldReceive('getReceiptItemsStatus')
                                                  ->once()
                                                  ->with($receipt)
                                                  ->andReturn($itemsStatus);

        $this->parentItemStateService->shouldReceive('findLowestStatusItems')
                                     ->once()
                                     ->andReturn($nextStatus);

        $this->stateTransitionHandlerService->shouldReceive('transitState')
                                            ->once()
                                            ->with($receipt, $nextStatus)
                                            ->andReturn();

        $stateSubscriberData = new StateSubscriberData($receiptItem, "DRAFT");

        $this->receiptStateDecisionMakerSubscriber->__invoke($stateSubscriberData);
    }
}
