<?php

namespace App\Dictionary;

class PullListSortedStatusDictionary extends Dictionary
{
    public const PULLLIST = [
        PullListStatusDictionary::DRAFT,
        PullListStatusDictionary::SENT_TO_LOCATOR,
        PullListStatusDictionary::CONFIRMED_BY_LOCATOR,
        PullListStatusDictionary::STOWING,
        PullListStatusDictionary::CLOSED,
    ];
}
