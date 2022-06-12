<?php

namespace App\Service\StatusTransition;

use App\DTO\StateSubscriberData;
use App\Service\StatusTransition\Exceptions\SubscriberClassNotFoundException;
use App\Service\StatusTransition\Exceptions\SubscriberClassNotValidException;
use App\Service\StatusTransition\Subscribers\StateSubscriberInterface;
use Psr\Container\ContainerInterface;

class StateSubscriberNotifier
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function notify(TransitionableInterface $entity, string $previousStatus): void
    {
        $stateSubscriberData = $this->createSubscriberDTO($entity, $previousStatus);
        $subscribers         = $entity->getStateSubscribers()->getSubscribers();

        foreach ($subscribers as $subscriberClass) {
            if (!class_exists($subscriberClass)) {
                throw new SubscriberClassNotFoundException("$subscriberClass class not found!");
            }

            $subscriber = $this->container->get($subscriberClass);
            if (!($subscriber instanceof StateSubscriberInterface)) {
                throw new SubscriberClassNotValidException("$subscriberClass is not a valid subscriber class");
            }

            $subscriber($stateSubscriberData);
        }
    }

    protected function createSubscriberDTO($entity, $previousStatus): StateSubscriberData
    {
        return new StateSubscriberData($entity, $previousStatus);
    }
}
