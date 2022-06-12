<?php

namespace App\Tests\Unit\Service\PullList\SentPullListToLocator;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Service\PullList\SentPullListToLocator\Exceptions\PullListHasNoItemException;
use App\Service\PullList\SentPullListToLocator\SentPullListToLocatorService;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class SentPullListToLocatorServiceTest extends BaseUnitTestCase
{
    protected PullList|LegacyMockInterface|MockInterface|null $pullListMock;

    protected LegacyMockInterface|PullListItem|MockInterface|null $pullListItemMock;

    protected StateTransitionHandlerService|LegacyMockInterface|MockInterface|null $stateTransitionHandlerMock;

    protected ?SentPullListToLocatorService $sentPullListToLocator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pullListMock               = Mockery::mock(PullList::class);
        $this->pullListItemMock           = Mockery::mock(PullListItem::class);
        $this->stateTransitionHandlerMock = Mockery::mock(StateTransitionHandlerService::class);

        $this->sentPullListToLocator = new SentPullListToLocatorService($this->stateTransitionHandlerMock);
    }

    public function testItCanNotSentPullListToLocatorWhenPullListHasNoItems(): void
    {
        $this->pullListMock->expects('hasItems')
                           ->withNoArgs()
                           ->andReturnFalse();

        self::expectException(PullListHasNoItemException::class);
        self::expectExceptionCode(400);
        self::expectExceptionMessage('Pull list has no items!');

        $this->sentPullListToLocator->perform($this->pullListMock);
    }

    public function testItCanSentPullListToLocator(): void
    {
        $this->pullListMock->expects('hasItems')
                           ->withNoArgs()
                           ->andReturnTrue();
        $this->pullListMock->expects('getItems')
                           ->withNoArgs()
                           ->andReturns(new ArrayCollection([$this->pullListItemMock, $this->pullListItemMock]));

        $this->stateTransitionHandlerMock->expects('batchTransitState')
                                         ->with(
                                             [$this->pullListItemMock, $this->pullListItemMock],
                                             PullListStatusDictionary::SENT_TO_LOCATOR
                                         )
                                         ->andReturns();

        $this->sentPullListToLocator->perform($this->pullListMock);
    }
}
