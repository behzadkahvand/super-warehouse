<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\ShowList;

use App\Dictionary\PickListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Admin;
use App\Entity\PickList;
use App\Entity\ReceiptItem;
use App\Repository\PickListRepository;
use App\Service\PickList\HandHeld\ShowList\HandHeldListService;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mockery;
use Symfony\Component\Security\Core\Security;

final class HandHeldListServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|EntityManagerInterface|Mockery\MockInterface|null $manager;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|Security|null $security;

    protected StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $stateTransitionHandlerService;

    protected Mockery\LegacyMockInterface|PickListRepository|Mockery\MockInterface|null $pickListRepository;

    protected HandHeldListService|null $handHeldListService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager                       = Mockery::mock(EntityManagerInterface::class);
        $this->security                      = Mockery::mock(Security::class);
        $this->stateTransitionHandlerService = Mockery::mock(StateTransitionHandlerService::class);
        $this->pickListRepository            = Mockery::mock(PickListRepository::class);

        $this->handHeldListService = new HandHeldListService(
            $this->manager,
            $this->security,
            $this->stateTransitionHandlerService,
            $this->pickListRepository
        );
    }

    public function testItCanGetListWhenPickerHasActivePickList(): void
    {
        $admin = Mockery::mock(Admin::class);
        $this->security->expects('getUser')
                       ->withNoArgs()
                       ->andReturns($admin);

        $this->pickListRepository->expects('getPickerAllActivePickList')
                                 ->with($admin)
                                 ->andReturns([1, 2]);

        $this->handHeldListService->getList(ReceiptTypeDictionary::GOOD_ISSUE);
    }

    public function testItCanGetListWhenPickerHasNotActivePickList(): void
    {
        $receiptType = ReceiptTypeDictionary::GOOD_ISSUE;

        $admin = Mockery::mock(Admin::class);
        $this->security->expects('getUser')
                       ->withNoArgs()
                       ->andReturns($admin);

        $this->pickListRepository->expects('getPickerAllActivePickList')
                                 ->with($admin)
                                 ->andReturns([]);

        $this->pickListRepository->expects('getHandHeldPickList')
                                 ->with($receiptType)
                                 ->andReturns([1, 2]);

        $this->handHeldListService->getList($receiptType);
    }

    public function testItCanConfirmList(): void
    {
        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturn();
        $this->manager->expects('flush')
                      ->withNoArgs()
                      ->andReturn();
        $this->manager->expects('commit')
                      ->withNoArgs()
                      ->andReturn();

        $admin = Mockery::mock(Admin::class);
        $this->security->shouldReceive('getUser')
                       ->twice()
                       ->withNoArgs()
                       ->andReturns($admin);

        $receiptITem = Mockery::mock(ReceiptItem::class);
        $receiptITem->shouldReceive('getId')
                    ->twice()
                    ->withNoArgs()
                    ->andReturn(1, 2);

        $pickList = Mockery::mock(PickList::class);
        $pickList->shouldReceive('getReceiptItem')
                 ->times(4)
                 ->withNoArgs()
                 ->andReturn($receiptITem);
        $pickList->shouldReceive('setPicker')
                 ->twice()
                 ->with($admin)
                 ->andReturnSelf();

        $pickLists = [$pickList, $pickList];

        $collection = new ArrayCollection($pickLists);

        $this->stateTransitionHandlerService->shouldReceive('batchTransitState')
                                            ->once()
                                            ->with($pickLists, PickListStatusDictionary::PICKING)
                                            ->andReturn();

        $this->stateTransitionHandlerService->shouldReceive('batchTransitState')
                                            ->once()
                                            ->with(
                                                [$receiptITem, $receiptITem],
                                                ReceiptStatusDictionary::PICKING
                                            )
                                            ->andReturn();

        $this->handHeldListService->confirmList($collection);
    }

    public function testItCanConfirmListWhenException(): void
    {
        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturn();

        $admin = Mockery::mock(Admin::class);
        $this->security->shouldReceive('getUser')
                       ->twice()
                       ->withNoArgs()
                       ->andReturns($admin);

        $receiptITem = Mockery::mock(ReceiptItem::class);
        $receiptITem->shouldReceive('getId')
                    ->twice()
                    ->withNoArgs()
                    ->andReturn(1, 2);

        $pickList = Mockery::mock(PickList::class);
        $pickList->shouldReceive('getReceiptItem')
                 ->times(4)
                 ->withNoArgs()
                 ->andReturn($receiptITem);
        $pickList->shouldReceive('setPicker')
                 ->twice()
                 ->with($admin)
                 ->andReturnSelf();

        $pickLists = [$pickList, $pickList];

        $collection = new ArrayCollection($pickLists);

        $this->stateTransitionHandlerService->shouldReceive('batchTransitState')
                                            ->once()
                                            ->with($pickLists, PickListStatusDictionary::PICKING)
                                            ->andThrow(new Exception());

        $this->manager->expects('close')->withNoArgs()->andReturn();
        $this->manager->expects('rollback')->withNoArgs()->andReturn();

        self::expectException(Exception::class);

        $this->handHeldListService->confirmList($collection);
    }
}
