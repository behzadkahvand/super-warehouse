<?php

namespace App\Tests\Unit\Service\Integration\Timcheh\EventStore;

use App\Document\Integration\Timcheh\EventStore;
use App\Service\Integration\Timcheh\EventStore\EventStoreFactory;
use App\Tests\Unit\BaseUnitTestCase;

class EventStoreFactoryTest extends BaseUnitTestCase
{
    public function testItCreateLogStore(): void
    {
        $eventStoreFactory = new EventStoreFactory();

        self::assertInstanceOf(EventStore::class, $eventStoreFactory->create());
    }
}
