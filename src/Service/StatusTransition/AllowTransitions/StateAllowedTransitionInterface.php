<?php

namespace App\Service\StatusTransition\AllowTransitions;

use App\DTO\AllowTransitionConfigData;

interface StateAllowedTransitionInterface
{
    public function __invoke(): AllowTransitionConfigData;
}
