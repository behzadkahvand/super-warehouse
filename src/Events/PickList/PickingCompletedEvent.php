<?php

namespace App\Events\PickList;

use App\Entity\PickList;
use Symfony\Contracts\EventDispatcher\Event;

final class PickingCompletedEvent extends Event
{
    public function __construct(private PickList $pickList)
    {
    }

    public function getPickList(): PickList
    {
        return $this->pickList;
    }
}
