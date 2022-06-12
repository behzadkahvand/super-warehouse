<?php

namespace App\Service\StatusTransition;

use App\DTO\AllowTransitionConfigData;
use App\DTO\StateSubscriberConfigData;

interface TransitionableInterface
{
    public function getStatePropertyName(): string;

    public function getAllowedTransitions(): AllowTransitionConfigData;

    public function getStateSubscribers(): StateSubscriberConfigData;
}
