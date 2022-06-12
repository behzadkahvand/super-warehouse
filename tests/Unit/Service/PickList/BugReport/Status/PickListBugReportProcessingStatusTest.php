<?php

namespace App\Tests\Unit\Service\PickList\BugReport\Status;

use App\Dictionary\PickListBugReportStatusDictionary;
use App\Entity\PickListBugReport;
use App\Service\PickList\BugReport\Status\PickListBugReportProcessingStatus;
use App\Service\StatusTransition\StateTransitionHandlerService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class PickListBugReportProcessingStatusTest extends MockeryTestCase
{
    public function testIsEligible(): void
    {
        $transitionService = Mockery::mock(StateTransitionHandlerService::class);
        $bugReport = Mockery::mock(PickListBugReport::class);
        $bugReport->shouldReceive('getStatus')
            ->once()
            ->withNoArgs()
            ->andReturn(PickListBugReportStatusDictionary::PROCESSING);

        $doneStatus = new PickListBugReportProcessingStatus($transitionService);

        self::assertTrue($doneStatus->isEligible($bugReport));
    }

    public function testApply(): void
    {
        $transitionService = Mockery::mock(StateTransitionHandlerService::class);
        $bugReport = Mockery::mock(PickListBugReport::class);
        $transitionService->shouldReceive('transitState')
            ->once()
            ->with($bugReport, Mockery::type('string'))
            ->andReturn();

        $doneStatus = new PickListBugReportProcessingStatus($transitionService);

        $doneStatus->apply($bugReport);
    }
}
