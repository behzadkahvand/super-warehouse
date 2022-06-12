<?php

namespace App\Tests\Unit\Service\PickList\BugReport;

use App\Entity\PickListBugReport;
use App\Service\PickList\BugReport\PickListBugReportFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class PickListBugReportFactoryTest extends MockeryTestCase
{
    public function testCreate(): void
    {
        $factory = new PickListBugReportFactory();

        self::assertInstanceOf(PickListBugReport::class, $factory->create());
    }
}
