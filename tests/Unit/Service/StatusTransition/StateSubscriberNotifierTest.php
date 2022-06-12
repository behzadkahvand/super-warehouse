<?php

namespace App\Tests\Unit\Service\StatusTransition;

use App\DTO\StateSubscriberConfigData;
use App\DTO\StateSubscriberData;
use App\Service\StatusTransition\Exceptions\SubscriberClassNotFoundException;
use App\Service\StatusTransition\Exceptions\SubscriberClassNotValidException;
use App\Service\StatusTransition\StateSubscriberNotifier;
use App\Service\StatusTransition\Subscribers\Receipt\ReceiptStateDecisionMakerSubscriber;
use App\Service\StatusTransition\Subscribers\StateSubscriberInterface;
use App\Service\StatusTransition\TransitionableInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Container\ContainerInterface;

class StateSubscriberNotifierTest extends MockeryTestCase
{
    private Mockery\LegacyMockInterface|TransitionableInterface|Mockery\MockInterface|null $transitionableInterface;

    private Mockery\LegacyMockInterface|Mockery\MockInterface|ContainerInterface|null $container;

    private StateSubscriberNotifier|null $stateSubscriberNotifier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container               = Mockery::mock(ContainerInterface::class);
        $this->transitionableInterface = Mockery::mock(TransitionableInterface::class);

        $this->stateSubscriberNotifier = new StateSubscriberNotifier($this->container);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->container               = null;
        $this->transitionableInterface = null;
        $this->stateSubscriberNotifier = null;
        Mockery::close();
    }

    public function testNotifyWhenClassNotFound(): void
    {
        $subscribers               = ["app/service/test1"];
        $stateSubscriberConfigData = Mockery::mock(StateSubscriberConfigData::class);
        $stateSubscriberConfigData->shouldReceive('getSubscribers')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturn($subscribers);
        $this->transitionableInterface->shouldReceive('getStateSubscribers')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($stateSubscriberConfigData);

        self::expectException(SubscriberClassNotFoundException::class);

        $this->stateSubscriberNotifier->notify($this->transitionableInterface, "DRAFT");
    }

    public function testNotifyWhenClassIsNotValid(): void
    {
        $subscribers               = [StateSubscriberConfigData::class];
        $stateSubscriberConfigData = Mockery::mock(StateSubscriberConfigData::class);
        $stateSubscriberConfigData->shouldReceive('getSubscribers')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturn($subscribers);
        $this->transitionableInterface->shouldReceive('getStateSubscribers')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($stateSubscriberConfigData);

        $this->container->shouldReceive('get')
                        ->once()
                        ->with($subscribers[0])
                        ->andReturn(Mockery::mock(StateSubscriberConfigData::class));

        self::expectException(SubscriberClassNotValidException::class);

        $this->stateSubscriberNotifier->notify($this->transitionableInterface, "DRAFT");
    }

    public function testNotifySuccess(): void
    {
        $subscribers               = [ReceiptStateDecisionMakerSubscriber::class];
        $stateSubscriberConfigData = Mockery::mock(StateSubscriberConfigData::class);
        $stateSubscriberConfigData->shouldReceive('getSubscribers')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturn($subscribers);
        $this->transitionableInterface->shouldReceive('getStateSubscribers')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($stateSubscriberConfigData);

        $subscriberMock = Mockery::mock(StateSubscriberInterface::class);
        $subscriberMock->shouldReceive('__invoke')
                       ->once()
                       ->with(Mockery::type(StateSubscriberData::class))
                       ->andReturn();

        $this->container->shouldReceive('get')
                        ->once()
                        ->with($subscribers[0])
                        ->andReturn($subscriberMock);

        $this->stateSubscriberNotifier->notify($this->transitionableInterface, "DRAFT");
    }
}
