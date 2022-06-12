<?php

namespace App\Tests\Unit\Service\StatusTransition\Subscribers\PullList;

use App\DTO\StateSubscriberData;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Service\StatusTransition\ParentItemStateService;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Service\StatusTransition\Subscribers\PullList\PullListStateDecisionMakerSubscriber;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class PullListStateDecisionMakerSubscriberTest extends BaseUnitTestCase
{
    private StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $stateTransitionHandlerService;

    private Mockery\LegacyMockInterface|ParentItemStateService|Mockery\MockInterface|null $parentItemStateService;

    protected Mockery\Mock|PullListStateDecisionMakerSubscriber|null $decisionMaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateTransitionHandlerService = Mockery::mock(StateTransitionHandlerService::class);
        $this->parentItemStateService        = Mockery::mock(ParentItemStateService::class);

        $this->decisionMaker = Mockery::mock(PullListStateDecisionMakerSubscriber::class, [
            $this->stateTransitionHandlerService,
            $this->parentItemStateService,
        ])
                                      ->makePartial()
                                      ->shouldAllowMockingProtectedMethods();
    }

    public function testItCanInvoke(): void
    {
        $pullList = Mockery::mock(PullList::class);
        $pullList->shouldReceive('getStatus')
                 ->twice()
                 ->withNoArgs()
                 ->andReturn("SENT_TO_LOCATOR");

        $pullListItem = Mockery::mock(PullListItem::class);
        $pullListItem->shouldReceive('getPullList')
                     ->once()
                     ->withNoArgs()
                     ->andReturn($pullList);
        $pullListItem->shouldReceive('getStatus')
                     ->once()
                     ->withNoArgs()
                     ->andReturn("CONFIRMED_BY_LOCATOR");

        $itemsStatus = ["CONFIRMED_BY_LOCATOR", "STOWING"];
        $this->decisionMaker->shouldReceive('getPullListItemsStatus')
                            ->once()
                            ->with($pullList)
                            ->andReturn($itemsStatus);

        $this->parentItemStateService->shouldReceive('findLowestStatusItems')
                                     ->once()
                                     ->andReturn("CONFIRMED_BY_LOCATOR");

        $this->stateTransitionHandlerService->shouldReceive('transitState')
                                            ->once()
                                            ->with($pullList, "CONFIRMED_BY_LOCATOR")
                                            ->andReturn();

        $stateSubscriberData = new StateSubscriberData($pullListItem, "DRAFT");

        $this->decisionMaker->__invoke($stateSubscriberData);
    }
}
