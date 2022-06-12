<?php

namespace App\Service\PickList\BugReport\Status;

use App\Entity\PickListBugReport;

interface PickListBugReportStatusInterface
{
    public function isEligible(PickListBugReport $pickListBugReport): bool;

    public function apply(PickListBugReport $pickListBugReport): void;
}
