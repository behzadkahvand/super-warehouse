<?php

namespace App\Tests\Unit\Service\StatusTransition;

use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\AllowTransitionConfigData;
use App\DTO\StateSubscriberConfigData;
use App\Entity\Admin;
use App\Service\StatusTransition\Exceptions\IllegalInitialStateException;
use App\Service\StatusTransition\Exceptions\IllegalStateTransitionException;
use App\Service\StatusTransition\StateSubscriberNotifier;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Service\StatusTransition\StateTransitionLogService;
use App\Service\StatusTransition\TransitionableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class StateTransitionHandlerServiceTest extends MockeryTestCase
{
    protected StateSubscriberNotifier|\Mockery\LegacyMockInterface|\Mockery\MockInterface|null $subscriberNotifier;

    protected \Mockery\LegacyMockInterface|EntityManagerInterface|\Mockery\MockInterface|null $entityManager;

    protected StateTransitionHandlerService|null $stateTransitionHandlerService;

    private Mockery\LegacyMockInterface|TransitionableInterface|Mockery\MockInterface|null $transitionableInterface;

    private Mockery\LegacyMockInterface|Mockery\MockInterface|StateTransitionLogService|null $transitionLogService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subscriberNotifier      = Mockery::mock(StateSubscriberNotifier::class);
        $this->entityManager           = Mockery::mock(EntityManagerInterface::class);
        $this->transitionableInterface = Mockery::mock(TransitionableInterface::class);
        $this->transitionLogService    = Mockery::mock(StateTransitionLogService::class);

        $this->stateTransitionHandlerService = new StateTransitionHandlerService(
            $this->subscriberNotifier,
            $this->entityManager,
            $this->transitionLogService
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->subscriberNotifier            = null;
        $this->entityManager                 = null;
        $this->transitionableInterface       = null;
        $this->transitionLogService          = null;
        $this->stateTransitionHandlerService = null;
        Mockery::close();
    }

    public function testBatchTransitStateDefaultValueNotDefined(): void
    {
        $this->entityManager->shouldReceive('beginTransaction')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        $this->entityManager->shouldReceive('close')->once()->withNoArgs()->andReturn();
        $this->entityManager->shouldReceive('rollback')->once()->withNoArgs()->andReturn();

        $this->transitionableInterface->shouldReceive('getStatePropertyName')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn("status");

        $this->transitionableInterface->shouldReceive('getStatus')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturnNull();

        $allowTransitionConfigData = Mockery::mock(AllowTransitionConfigData::class);
        $allowTransitionConfigData->shouldReceive('getDefault')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturnNull();
        $this->transitionableInterface->shouldReceive('getAllowedTransitions')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($allowTransitionConfigData);

        self::expectException(IllegalInitialStateException::class);

        $this->stateTransitionHandlerService->batchTransitState([$this->transitionableInterface], "DRAFT");
    }

    public function testBatchTransitStateWhenOneTransitionIsRecursiveAndAnotherIsSuccess(): void
    {
        $currentStatus = ReceiptStatusDictionary::DRAFT;
        $nextStatus    = ReceiptStatusDictionary::APPROVED;
        $admin         = Mockery::mock(Admin::class);

        $this->entityManager->shouldReceive('beginTransaction')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        $this->transitionableInterface->shouldReceive('getStatePropertyName')
                                      ->twice()
                                      ->withNoArgs()
                                      ->andReturn("status");

        $this->transitionableInterface->shouldReceive('getStatus')
                                      ->twice()
                                      ->withNoArgs()
                                      ->andReturn($currentStatus);

        $this->transitionableInterface->shouldReceive('getId')
                                      ->twice()
                                      ->withNoArgs()
                                      ->andReturn(1);

        $allowTransitionConfigData = Mockery::mock(AllowTransitionConfigData::class);
        $allowTransitionConfigData->shouldReceive('isTransitionAllowed')
                                  ->once()
                                  ->with($currentStatus, $nextStatus)
                                  ->andReturnTrue();

        $this->transitionableInterface->shouldReceive('getAllowedTransitions')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($allowTransitionConfigData);

        $this->transitionableInterface->shouldReceive('setStatus')
                                      ->once()
                                      ->with($nextStatus)
                                      ->andReturnSelf();

        $stateSubscriberConfigData = Mockery::mock(StateSubscriberConfigData::class);
        $stateSubscriberConfigData->shouldReceive('hasSubscriber')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturnTrue();
        $this->transitionableInterface->shouldReceive('getStateSubscribers')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($stateSubscriberConfigData);

        $this->subscriberNotifier->shouldReceive('notify')
                                 ->once()
                                 ->with($this->transitionableInterface, $currentStatus)
                                 ->andReturn();

        $this->transitionLogService->shouldReceive('addLog')
                                   ->once()
                                   ->with($this->transitionableInterface, $currentStatus, $nextStatus)
                                   ->andReturnSelf();
        $this->transitionLogService->shouldReceive('setUser')
                                   ->once()
                                   ->with($admin)
                                   ->andReturnSelf();
        $this->transitionLogService->shouldReceive('persist')
                                   ->once()
                                   ->withNoArgs()
                                   ->andReturn();

        $this->entityManager->shouldReceive('flush')->once()->withNoArgs()->andReturn();
        $this->entityManager->shouldReceive('commit')->once()->withNoArgs()->andReturn();

        $this->stateTransitionHandlerService->batchTransitState(
            [
            $this->transitionableInterface,
            $this->transitionableInterface,
            ],
            $nextStatus,
            $admin
        );
    }

    public function testBatchTransitStateWhenTransitionIsNotLegal(): void
    {
        $currentStatus = "DRAFT";
        $nextStatus    = "READY_TO_PEEK";

        $this->entityManager->shouldReceive('beginTransaction')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        $this->entityManager->shouldReceive('close')->once()->withNoArgs()->andReturn();
        $this->entityManager->shouldReceive('rollback')->once()->withNoArgs()->andReturn();

        $this->transitionableInterface->shouldReceive('getStatePropertyName')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn("status");

        $this->transitionableInterface->shouldReceive('getStatus')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($currentStatus);

        $this->transitionableInterface->shouldReceive('getId')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn(8);

        $allowTransitionConfigData = Mockery::mock(AllowTransitionConfigData::class);
        $allowTransitionConfigData->shouldReceive('isTransitionAllowed')
                                  ->once()
                                  ->with($currentStatus, $nextStatus)
                                  ->andReturnFalse();
        $this->transitionableInterface->shouldReceive('getAllowedTransitions')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($allowTransitionConfigData);

        self::expectException(IllegalStateTransitionException::class);

        $this->stateTransitionHandlerService->batchTransitState([$this->transitionableInterface], $nextStatus);
    }

    public function testTransitStateWhenTransitionIsSuccess(): void
    {
        $currentStatus = "APPROVED";
        $nextStatus    = "READY_TO_PEEK";
        $admin         = null;

        $this->entityManager->shouldReceive('beginTransaction')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        $this->transitionableInterface->shouldReceive('getStatePropertyName')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn("status");

        $this->transitionableInterface->shouldReceive('getStatus')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($currentStatus);

        $allowTransitionConfigData = Mockery::mock(AllowTransitionConfigData::class);
        $allowTransitionConfigData->shouldReceive('isTransitionAllowed')
                                  ->once()
                                  ->with($currentStatus, $nextStatus)
                                  ->andReturnTrue();

        $this->transitionableInterface->shouldReceive('getAllowedTransitions')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($allowTransitionConfigData);

        $this->transitionableInterface->shouldReceive('setStatus')
                                      ->once()
                                      ->with($nextStatus)
                                      ->andReturnSelf();

        $this->transitionableInterface->shouldReceive('getId')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn(5);

        $stateSubscriberConfigData = Mockery::mock(StateSubscriberConfigData::class);
        $stateSubscriberConfigData->shouldReceive('hasSubscriber')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturnTrue();
        $this->transitionableInterface->shouldReceive('getStateSubscribers')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($stateSubscriberConfigData);

        $this->subscriberNotifier->shouldReceive('notify')
                                 ->once()
                                 ->with($this->transitionableInterface, $currentStatus)
                                 ->andReturn();

        $this->transitionLogService->shouldReceive('addLog')
                                   ->once()
                                   ->with($this->transitionableInterface, $currentStatus, $nextStatus)
                                   ->andReturnSelf();
        $this->transitionLogService->shouldReceive('setUser')
                                   ->once()
                                   ->with($admin)
                                   ->andReturnSelf();
        $this->transitionLogService->shouldReceive('persist')
                                   ->once()
                                   ->withNoArgs()
                                   ->andReturn();

        $this->entityManager->shouldReceive('flush')->once()->withNoArgs()->andReturn();
        $this->entityManager->shouldReceive('commit')->once()->withNoArgs()->andReturn();

        $this->stateTransitionHandlerService->transitState($this->transitionableInterface, $nextStatus, $admin);
    }

    public function testBatchTransitStateWhenSubscribersHasException(): void
    {
        $currentStatus = "READY_TO_PEEK";
        $nextStatus    = "PEEKING";

        $this->entityManager->shouldReceive('beginTransaction')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        $this->transitionableInterface->shouldReceive('getStatePropertyName')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn("status");

        $this->transitionableInterface->shouldReceive('getStatus')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($currentStatus);

        $this->transitionableInterface->shouldReceive('getId')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn(4);

        $allowTransitionConfigData = Mockery::mock(AllowTransitionConfigData::class);
        $allowTransitionConfigData->shouldReceive('isTransitionAllowed')
                                  ->once()
                                  ->with($currentStatus, $nextStatus)
                                  ->andReturnTrue();

        $this->transitionableInterface->shouldReceive('getAllowedTransitions')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($allowTransitionConfigData);

        $this->transitionableInterface->shouldReceive('setStatus')
                                      ->once()
                                      ->with($nextStatus)
                                      ->andReturnSelf();

        $stateSubscriberConfigData = Mockery::mock(StateSubscriberConfigData::class);
        $stateSubscriberConfigData->shouldReceive('hasSubscriber')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturnTrue();
        $this->transitionableInterface->shouldReceive('getStateSubscribers')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($stateSubscriberConfigData);

        $this->subscriberNotifier->shouldReceive('notify')
                                 ->once()
                                 ->with($this->transitionableInterface, $currentStatus)
                                 ->andThrow(new Exception());

        $this->entityManager->shouldReceive('close')->once()->withNoArgs()->andReturn();
        $this->entityManager->shouldReceive('rollback')->once()->withNoArgs()->andReturn();

        self::expectException(Exception::class);

        $this->stateTransitionHandlerService->batchTransitState([$this->transitionableInterface], $nextStatus);
    }
}
