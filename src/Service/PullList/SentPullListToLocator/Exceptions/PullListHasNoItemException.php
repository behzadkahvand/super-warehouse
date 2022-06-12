<?php

namespace App\Service\PullList\SentPullListToLocator\Exceptions;

class PullListHasNoItemException extends SentPullListToLocatorException
{
    protected $message = 'Pull list has no items!';
}
