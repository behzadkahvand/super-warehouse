<?php

namespace App\Service\Integration\Timcheh\EventStore;

use App\Document\Integration\Timcheh\EventStore;

class EventStoreFactory
{
    public function create(): EventStore
    {
        return new EventStore();
    }
}
