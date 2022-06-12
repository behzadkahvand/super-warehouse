<?php

namespace App\Service\Integration\Timcheh\LogStore;

use App\Document\Integration\Timcheh\LogStore;

class LogStoreFactory
{
    public function create(): LogStore
    {
        return new LogStore();
    }
}
