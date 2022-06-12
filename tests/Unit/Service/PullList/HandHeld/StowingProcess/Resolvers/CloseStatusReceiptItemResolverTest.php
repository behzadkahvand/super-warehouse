<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\PullListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\ReceiptItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\CloseStatusReceiptItemResolver;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class CloseStatusReceiptItemResolverTest extends BaseUnitTestCase
{
    protected StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $transitionHandler;

    protected CloseStatusReceiptItemResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transitionHandler = Mockery::mock(StateTransitionHandlerService::class);

        $this->resolver = new CloseStatusReceiptItemResolver($this->transitionHandler);
    }

    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);
        $receiptItem  = Mockery::mock(ReceiptItem::class);

        $pullListItem->expects('getStatus')
                     ->withNoArgs()
                     ->andReturn(PullListStatusDictionary::CLOSED);
        $pullListItem->expects('getReceiptItem')
                     ->withNoArgs()
                     ->andReturn($receiptItem);

        $this->transitionHandler->expects('transitState')
                                ->with($receiptItem, ReceiptStatusDictionary::DONE)
                                ->andReturn();

        $this->resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(8, $this->resolver->getPriority());
    }
}
