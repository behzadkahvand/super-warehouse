<?php

namespace App\Document\StatusTransitionLog;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="pick_list_bug_report_status_logs")
 */
class PickListBugReportStatusLog extends StatusTransitionLog
{
}
