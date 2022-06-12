<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\PickListStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\PickList\HandHeld\Picking\Resolvers\ClosePickListStatusResolver;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

final class ClosePickListStatusResolverTest extends BaseUnitTestCase
{
    protected StateTransitionHandlerService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $stateTransitionHandler;

    protected ClosePickListStatusResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stateTransitionHandler = Mockery::mock(StateTransitionHandlerService::class);

        $this->resolver = new ClosePickListStatusResolver($this->stateTransitionHandler);
    }

    public function testItCanResolve(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);

        $pickList = Mockery::mock(PickList::class);
        $pickList->expects('getRemainedQuantity')
                  ->withNoArgs()
                  ->andReturn(0);

        $this->stateTransitionHandler->expects('transitState')
                                     ->with($pickList, PickListStatusDictionary::CLOSE)
                                     ->andReturn();

        $this->resolver->resolve($pickList, $itemSerial);
    }

    public function testPriority(): void
    {
        self::assertEquals($this->resolver::getPriority(), 8);
    }
}
