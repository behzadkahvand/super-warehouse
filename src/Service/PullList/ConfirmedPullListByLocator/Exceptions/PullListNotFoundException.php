<?php

namespace App\Service\PullList\ConfirmedPullListByLocator\Exceptions;

class PullListNotFoundException extends ConfirmedPullListByLocatorException
{
    protected $message = 'Pull list not found for confirming!';
}
