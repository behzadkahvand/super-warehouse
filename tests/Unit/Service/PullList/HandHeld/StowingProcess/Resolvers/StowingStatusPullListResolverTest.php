<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\StowingStatusPullListResolver;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class StowingStatusPullListResolverTest extends BaseUnitTestCase
{
    protected StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $transitionHandler;

    protected StowingStatusPullListResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transitionHandler = Mockery::mock(StateTransitionHandlerService::class);

        $this->resolver = new StowingStatusPullListResolver($this->transitionHandler);
    }

    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $pullList->expects('getStatus')
                 ->withNoArgs()
                 ->andReturn(PullListStatusDictionary::CONFIRMED_BY_LOCATOR);

        $this->transitionHandler->expects('transitState')
                                ->with($pullList, PullListStatusDictionary::STOWING)
                                ->andReturn();

        $this->resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(11, $this->resolver->getPriority());
    }
}
