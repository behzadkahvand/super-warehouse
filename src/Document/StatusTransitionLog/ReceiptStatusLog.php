<?php

namespace App\Document\StatusTransitionLog;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="receipt_status_logs")
 */
class ReceiptStatusLog extends StatusTransitionLog
{
}
