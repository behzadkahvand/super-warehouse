<?php

namespace App\Service\PickList\BugReport\Status;

use App\Dictionary\PickListBugReportStatusDictionary;
use App\Entity\PickListBugReport;

final class PickListBugReportPendingStatus extends AbstractPickListBugReportStatus
{
    public function isEligible(PickListBugReport $pickListBugReport): bool
    {
        return $pickListBugReport->getStatus() === PickListBugReportStatusDictionary::PENDING;
    }

    public function apply(PickListBugReport $pickListBugReport): void
    {
        $this->transitionHandlerService->transitState($pickListBugReport, PickListBugReportStatusDictionary::PROCESSING);
    }
}
