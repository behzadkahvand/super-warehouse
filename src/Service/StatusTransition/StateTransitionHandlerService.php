<?php

namespace App\Service\StatusTransition;

use App\Entity\Admin;
use App\Service\StatusTransition\Exceptions\IllegalStateTransitionException;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class StateTransitionHandlerService extends BaseStateTransitionHandler
{
    public function __construct(
        private StateSubscriberNotifier $stateSubscriberNotifier,
        private EntityManagerInterface $entityManager,
        private StateTransitionLogService $transitionLogService
    ) {
    }

    public function transitState(
        TransitionableInterface $entityObject,
        string $nextState,
        ?Admin $transitionBy = null
    ): void {
        $this->batchTransitState([$entityObject], $nextState, $transitionBy);
    }

    public function batchTransitState(array $entityObjects, string $nextState, ?Admin $transitionBy = null): void
    {
        $this->entityManager->beginTransaction();

        try {
            foreach ($entityObjects as $entityObject) {
                $statePropertyName = ucfirst($entityObject->getStatePropertyName());

                $currentState = $this->getCurrentState($entityObject, $statePropertyName);

                if ($this->isRecursiveTransition($entityObject, $currentState, $nextState)) {
                    continue;
                }

                if (!$entityObject->getAllowedTransitions()->isTransitionAllowed($currentState, $nextState)) {
                    throw new IllegalStateTransitionException("$nextState is not legal state for next state of $currentState");
                }

                $entityObject->{'set' . $statePropertyName}($nextState);

                if ($entityObject->getStateSubscribers()->hasSubscriber()) {
                    $this->stateSubscriberNotifier->notify($entityObject, $currentState);
                }

                $this->transitionLogService->addLog($entityObject, $currentState, $nextState);
            }

            $this->entityManager->flush();

            $this->transitionLogService->setUser($transitionBy)->persist();

            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->close();
            $this->entityManager->rollback();

            throw $exception;
        }
    }
}
