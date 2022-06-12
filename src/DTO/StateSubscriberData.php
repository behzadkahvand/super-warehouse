<?php

namespace App\DTO;

use App\Service\StatusTransition\TransitionableInterface;

final class StateSubscriberData extends BaseDTO
{
    public function __construct(protected TransitionableInterface $entityObject, protected string $previousStatus)
    {
    }

    public function getPreviousState(): string
    {
        return $this->previousStatus;
    }

    public function getEntityObject(): TransitionableInterface
    {
        return $this->entityObject;
    }
}
