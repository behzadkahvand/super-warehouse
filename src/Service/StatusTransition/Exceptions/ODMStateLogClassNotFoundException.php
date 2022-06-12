<?php

namespace App\Service\StatusTransition\Exceptions;

use App\Service\ExceptionHandler\RenderableThrowableInterface;
use App\Service\ExceptionHandler\ReportableThrowableInterface;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ODMStateLogClassNotFoundException extends Exception implements
    RenderableThrowableInterface,
    ReportableThrowableInterface
{
    protected $message = "Document state log class not found!";

    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;

    public function getMetadata(TranslatorInterface $translator): ThrowableMetadata
    {
        return new ThrowableMetadata(false, $this->getCode(), $this->getMessage());
    }

    public function shouldReport(): bool
    {
        return false;
    }
}
