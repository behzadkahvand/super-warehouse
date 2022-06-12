<?php

namespace App\Document\StatusTransitionLog;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="receipt_item_status_logs")
 */
class ReceiptItemStatusLog extends StatusTransitionLog
{
}
