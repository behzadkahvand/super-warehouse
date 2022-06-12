<?php

namespace App\Dictionary;

class PullListStatusDictionary extends Dictionary
{
    public const DRAFT = 'DRAFT';
    public const SENT_TO_LOCATOR = 'SENT_TO_LOCATOR';
    public const CONFIRMED_BY_LOCATOR = 'CONFIRMED_BY_LOCATOR';
    public const STOWING = 'STOWING';
    public const CLOSED = 'CLOSED';
}
