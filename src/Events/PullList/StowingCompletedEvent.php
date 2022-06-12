<?php

namespace App\Events\PullList;

use App\Entity\PullListItem;
use Symfony\Contracts\EventDispatcher\Event;

final class StowingCompletedEvent extends Event
{
    public function __construct(private PullListItem $pullListItem)
    {
    }

    public function getPullListItem(): PullListItem
    {
        return $this->pullListItem;
    }
}
