<?php

namespace App\Tests\Unit\Service\Integration\Timcheh\LogStore;

use App\Document\Integration\Timcheh\LogStore;
use App\Service\Integration\Timcheh\LogStore\LogStoreFactory;
use App\Tests\Unit\BaseUnitTestCase;

class LogStoreFactoryTest extends BaseUnitTestCase
{
    public function testItCreateLogStore(): void
    {
        $logStoreFactory = new LogStoreFactory();

        self::assertInstanceOf(LogStore::class, $logStoreFactory->create());
    }
}
