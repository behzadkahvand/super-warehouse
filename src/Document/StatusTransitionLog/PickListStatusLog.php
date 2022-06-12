<?php

namespace App\Document\StatusTransitionLog;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="pick_list_status_logs")
 */
class PickListStatusLog extends StatusTransitionLog
{
}
