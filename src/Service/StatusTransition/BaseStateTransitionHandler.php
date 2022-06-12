<?php

namespace App\Service\StatusTransition;

use App\Service\StatusTransition\Exceptions\IllegalInitialStateException;

abstract class BaseStateTransitionHandler
{
    protected static array $calledTransitions = [];

    protected function getCurrentState(TransitionableInterface $entityObject, string $statePropertyName): string
    {
        $currentState = $entityObject->{'get' . $statePropertyName}();

        if (!$currentState) {
            $currentState = $entityObject->getAllowedTransitions()->getDefault() ??
                throw new IllegalInitialStateException();
        }

        return $currentState;
    }

    protected function isRecursiveTransition(
        TransitionableInterface $entityObject,
        string $currentState,
        string $nextState
    ): bool {
        if ($currentState === $nextState) {
            return true;
        }

        $transitionKey = $this->getTransitionKey($entityObject);

        if (in_array($transitionKey, static::$calledTransitions)) {
            return true;
        }

        static::$calledTransitions[] = $transitionKey;

        return false;
    }

    private function getTransitionKey(TransitionableInterface $entityObject): string
    {
        return get_class($entityObject) . "_" . $entityObject->getId();
    }
}
