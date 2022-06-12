<?php

namespace App\Service\PickList\BugReport;

use App\Entity\PickListBugReport;

final class PickListBugReportFactory
{
    public function create(): PickListBugReport
    {
        return new PickListBugReport();
    }
}
