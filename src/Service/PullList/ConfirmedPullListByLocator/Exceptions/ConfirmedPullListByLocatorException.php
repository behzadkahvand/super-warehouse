<?php

namespace App\Service\PullList\ConfirmedPullListByLocator\Exceptions;

use App\Service\ExceptionHandler\ReportableThrowableInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ConfirmedPullListByLocatorException extends Exception implements ReportableThrowableInterface
{
    protected $code =  Response::HTTP_BAD_REQUEST;

    public function shouldReport(): bool
    {
        return false;
    }
}
