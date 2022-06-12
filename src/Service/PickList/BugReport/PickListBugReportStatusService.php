<?php

namespace App\Service\PickList\BugReport;

use App\Entity\PickListBugReport;
use App\Service\PickList\BugReport\Status\PickListBugReportStatusInterface;

final class PickListBugReportStatusService
{
    public function __construct(private iterable $statuses)
    {
    }

    public function update(PickListBugReport $pickListBugReport): PickListBugReport
    {
        /** @var PickListBugReportStatusInterface $status */
        foreach ($this->statuses as $status) {
            if ($status->isEligible($pickListBugReport)) {
                $status->apply($pickListBugReport);
            }
        }

        return $pickListBugReport;
    }
}
