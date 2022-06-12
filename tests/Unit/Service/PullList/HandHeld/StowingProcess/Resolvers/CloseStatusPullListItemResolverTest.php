<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\CloseStatusPullListItemResolver;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class CloseStatusPullListItemResolverTest extends BaseUnitTestCase
{
    protected StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $transitionHandler;

    protected CloseStatusPullListItemResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transitionHandler = Mockery::mock(StateTransitionHandlerService::class);

        $this->resolver = new CloseStatusPullListItemResolver($this->transitionHandler);
    }

    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $pullListItem->expects('getRemainQuantity')
                     ->withNoArgs()
                     ->andReturn(0);

        $this->transitionHandler->expects('transitState')
                                ->with($pullListItem, PullListStatusDictionary::CLOSED)
                                ->andReturn();

        $this->resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(10, $this->resolver->getPriority());
    }
}
