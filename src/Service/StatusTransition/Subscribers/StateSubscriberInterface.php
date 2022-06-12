<?php

namespace App\Service\StatusTransition\Subscribers;

use App\DTO\StateSubscriberData;

interface StateSubscriberInterface
{
    public function __invoke(StateSubscriberData $stateSubscriberData): void;
}
