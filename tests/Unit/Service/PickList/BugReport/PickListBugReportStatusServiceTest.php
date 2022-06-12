<?php

namespace App\Tests\Unit\Service\PickList\BugReport;

use App\Entity\PickListBugReport;
use App\Service\PickList\BugReport\PickListBugReportStatusService;
use App\Service\PickList\BugReport\Status\PickListBugReportDoneStatus;
use App\Service\PickList\BugReport\Status\PickListBugReportPendingStatus;
use App\Service\PickList\BugReport\Status\PickListBugReportProcessingStatus;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class PickListBugReportStatusServiceTest extends MockeryTestCase
{
    public function testUpdate(): void
    {
        $pending = Mockery::mock(PickListBugReportPendingStatus::class);
        $processing = Mockery::mock(PickListBugReportProcessingStatus::class);
        $done = Mockery::mock(PickListBugReportDoneStatus::class);
        $bugReport = Mockery::mock(PickListBugReport::class);

        $pending->shouldReceive('isEligible')
            ->once()
            ->with($bugReport)
            ->andReturnFalse();

        $processing->shouldReceive('isEligible')
                ->once()
                ->with($bugReport)
                ->andReturnFalse();

        $done->shouldReceive('isEligible')
                ->once()
                ->with($bugReport)
                ->andReturnTrue();

        $done->shouldReceive('apply')
             ->once()
             ->with($bugReport)
             ->andReturn();

        $service = new PickListBugReportStatusService([$pending, $processing, $done]);

        self::assertSame($bugReport, $service->update($bugReport));
    }
}
