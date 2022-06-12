<?php

namespace App\Service\StatusTransition\Exceptions;

use App\Service\ExceptionHandler\RenderableThrowableInterface;
use App\Service\ExceptionHandler\ReportableThrowableInterface;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class IllegalStateTransitionException extends Exception implements
    RenderableThrowableInterface,
    ReportableThrowableInterface
{
    protected $message = "Illegal state transition!";

    protected $code = Response::HTTP_BAD_REQUEST;

    public function getMetadata(TranslatorInterface $translator): ThrowableMetadata
    {
        return new ThrowableMetadata(true, $this->getCode(), $this->getMessage());
    }

    public function shouldReport(): bool
    {
        return true;
    }
}
