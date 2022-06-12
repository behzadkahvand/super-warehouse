<?php

namespace App\Service\PickList\BugReport\Status;

use App\Dictionary\PickListBugReportStatusDictionary;
use App\Entity\PickListBugReport;

final class PickListBugReportProcessingStatus extends AbstractPickListBugReportStatus
{
    public function isEligible(PickListBugReport $pickListBugReport): bool
    {
        return $pickListBugReport->getStatus() === PickListBugReportStatusDictionary::PROCESSING;
    }

    public function apply(PickListBugReport $pickListBugReport): void
    {
        $this->transitionHandlerService->transitState($pickListBugReport, PickListBugReportStatusDictionary::DONE);
    }
}
