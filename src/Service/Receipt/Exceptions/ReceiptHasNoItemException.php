<?php

namespace App\Service\Receipt\Exceptions;

use App\Service\ExceptionHandler\RenderableThrowableInterface;
use App\Service\ExceptionHandler\ReportableThrowableInterface;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReceiptHasNoItemException extends Exception implements RenderableThrowableInterface, ReportableThrowableInterface
{
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    protected $message = 'The receipt must have at least one item!';

    public function getMetadata(TranslatorInterface $translator): ThrowableMetadata
    {
        return new ThrowableMetadata(true, $this->getCode(), $this->getMessage());
    }

    public function shouldReport(): bool
    {
        return false;
    }
}
