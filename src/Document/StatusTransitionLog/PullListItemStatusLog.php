<?php

namespace App\Document\StatusTransitionLog;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="pull_list_item_status_logs")
 */
class PullListItemStatusLog extends StatusTransitionLog
{
}
