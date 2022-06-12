<?php

namespace App\Dictionary;

class LogStoreStatusDictionary extends Dictionary
{
    public const SENT = "SENT";
    public const PROCESSING = "PROCESSING";
    public const PROCESSED = "PROCESSED";
    public const FAILED = "FAILED";
    public const SENT_TO_FAILURE = "SENT_TO_FAILURE";
}
