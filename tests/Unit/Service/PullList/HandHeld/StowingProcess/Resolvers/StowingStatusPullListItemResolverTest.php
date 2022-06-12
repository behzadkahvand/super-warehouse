<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\StowingStatusPullListItemResolver;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class StowingStatusPullListItemResolverTest extends BaseUnitTestCase
{
    protected StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $transitionHandler;

    protected StowingStatusPullListItemResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transitionHandler = Mockery::mock(StateTransitionHandlerService::class);

        $this->resolver = new StowingStatusPullListItemResolver($this->transitionHandler);
    }

    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $pullListItem->expects('getQuantity')
                     ->withNoArgs()
                     ->andReturn(2);
        $pullListItem->expects('getRemainQuantity')
                     ->withNoArgs()
                     ->andReturn(1);

        $this->transitionHandler->expects('transitState')
                                ->with($pullListItem, PullListStatusDictionary::STOWING)
                                ->andReturn();

        $this->resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(12, $this->resolver->getPriority());
    }
}
