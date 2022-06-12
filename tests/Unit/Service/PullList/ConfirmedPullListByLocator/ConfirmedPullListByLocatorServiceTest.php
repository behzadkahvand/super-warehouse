<?php

namespace App\Tests\Unit\Service\PullList\ConfirmedPullListByLocator;

use App\Dictionary\PullListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Admin;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\ReceiptItem;
use App\Repository\PullListRepository;
use App\Service\PullList\ConfirmedPullListByLocator\ConfirmedPullListByLocatorService;
use App\Service\PullList\ConfirmedPullListByLocator\Exceptions\PullListNotFoundException;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class ConfirmedPullListByLocatorServiceTest extends BaseUnitTestCase
{
    protected PullList|LegacyMockInterface|MockInterface|null $pullListMock;

    protected LegacyMockInterface|PullListItem|MockInterface|null $pullListItemMock;

    protected Admin|LegacyMockInterface|MockInterface|null $adminMock;

    protected LegacyMockInterface|ReceiptItem|MockInterface|null $receiptItemMock;

    protected LegacyMockInterface|MockInterface|PullListRepository|null $pullListRepoMock;

    protected StateTransitionHandlerService|LegacyMockInterface|MockInterface|null $stateTransitionHandlerMock;

    protected ?ConfirmedPullListByLocatorService $confirmedPullListByLocator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pullListMock               = Mockery::mock(PullList::class);
        $this->pullListItemMock           = Mockery::mock(PullListItem::class);
        $this->adminMock                  = Mockery::mock(Admin::class);
        $this->receiptItemMock            = Mockery::mock(ReceiptItem::class);
        $this->pullListRepoMock           = Mockery::mock(PullListRepository::class);
        $this->stateTransitionHandlerMock = Mockery::mock(StateTransitionHandlerService::class);

        $this->confirmedPullListByLocator = new ConfirmedPullListByLocatorService(
            $this->pullListRepoMock,
            $this->stateTransitionHandlerMock
        );
    }

    public function testItCanNotConfirmedPullListByLocatorWhenPullListNotFound(): void
    {
        $this->pullListRepoMock->expects('findOneBy')
                               ->with([
                                   'id'      => 12,
                                   'locator' => $this->adminMock,
                                   'status'  => PullListStatusDictionary::SENT_TO_LOCATOR
                               ])
                               ->andReturnNull();

        self::expectException(PullListNotFoundException::class);
        self::expectExceptionCode(400);
        self::expectExceptionMessage('Pull list not found for confirming!');

        $this->confirmedPullListByLocator->perform(12, $this->adminMock);
    }

    public function testItCanConfirmedPullListByLocator(): void
    {
        $this->pullListRepoMock->expects('findOneBy')
                               ->with([
                                   'id'      => 12,
                                   'locator' => $this->adminMock,
                                   'status'  => PullListStatusDictionary::SENT_TO_LOCATOR
                               ])
                               ->andReturns($this->pullListMock);

        $this->pullListMock->expects('getItems')
                           ->withNoArgs()
                           ->andReturns(new ArrayCollection([$this->pullListItemMock, $this->pullListItemMock]));

        $this->pullListItemMock->expects('getReceiptItem')
                               ->twice()
                               ->withNoArgs()
                               ->andReturns($this->receiptItemMock);

        $this->stateTransitionHandlerMock->expects('batchTransitState')
                                         ->with(
                                             [$this->pullListItemMock, $this->pullListItemMock],
                                             PullListStatusDictionary::CONFIRMED_BY_LOCATOR,
                                             $this->adminMock
                                         )
                                         ->andReturns();

        $this->stateTransitionHandlerMock->expects('batchTransitState')
                                         ->with(
                                             [$this->receiptItemMock, $this->receiptItemMock],
                                             ReceiptStatusDictionary::STOWING,
                                             $this->adminMock
                                         )
                                         ->andReturns();

        $result = $this->confirmedPullListByLocator->perform(12, $this->adminMock);

        self::assertEquals($this->pullListMock, $result);
    }
}
