<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\PickListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Repository\PickListRepository;
use App\Service\PickList\HandHeld\Picking\Resolvers\CloseReceiptStatusResolver;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;

final class CloseReceiptStatusResolverTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|StateTransitionHandlerService|Mockery\MockInterface|null $stateTransitionHandler;

    protected Mockery\LegacyMockInterface|PickListRepository|Mockery\MockInterface|null $pickListRepository;

    protected CloseReceiptStatusResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateTransitionHandler = Mockery::mock(StateTransitionHandlerService::class);
        $this->pickListRepository     = Mockery::mock(PickListRepository::class);

        $this->resolver = new CloseReceiptStatusResolver($this->stateTransitionHandler, $this->pickListRepository);
    }

    public function testItCanResolve(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);

        $pickList = Mockery::mock(PickList::class);
        $pickList->shouldReceive('getStatus')
                 ->twice()
                 ->withNoArgs()
                 ->andReturn(PickListStatusDictionary::CLOSE);

        $receiptItem  = Mockery::mock(ReceiptItem::class);
        $receipt      = Mockery::mock(Receipt::class);
        $receiptItems = [$receiptItem, $receiptItem];
        $collection   = new ArrayCollection($receiptItems);

        $receiptItem->shouldReceive('getReceipt')
                    ->once()
                    ->withNoArgs()
                    ->andReturn($receipt);

        $receipt->shouldReceive('getReceiptItems')
                ->once()
                ->withNoArgs()
                ->andReturn($collection);

        $pickList->shouldReceive('getReceiptItem')
                 ->once()
                 ->withNoArgs()
                 ->andReturn($receiptItem);

        $pickList1 = Mockery::mock(PickList::class);
        $pickList1->shouldReceive('getStatus')
                  ->once()
                  ->withNoArgs()
                  ->andReturn(PickListStatusDictionary::CLOSE);

        $resultPickList = [$pickList1, $pickList];

        $this->pickListRepository->shouldReceive('getAllReceiptPickList')
                                 ->once()
                                 ->with($receipt)
                                 ->andReturn($resultPickList);

        $this->stateTransitionHandler->shouldReceive('batchTransitState')
                                     ->once()
                                     ->with($receiptItems, ReceiptStatusDictionary::DONE)
                                     ->andReturn();

        $this->resolver->resolve($pickList, $itemSerial);
    }

    public function testPriority(): void
    {
        self::assertEquals($this->resolver::getPriority(), 6);
    }
}
