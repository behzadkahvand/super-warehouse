<?php

namespace App\Service\Relocate\Exceptions;

use App\Service\ExceptionHandler\RenderableThrowableInterface;
use App\Service\ExceptionHandler\ReportableThrowableInterface;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ItemRelocateReserveStockLimitException extends Exception implements
    RenderableThrowableInterface,
    ReportableThrowableInterface
{
    protected $code = Response::HTTP_BAD_REQUEST;

    protected $message = "You can't relocate this item because this item is reserved stock!";

    public function getMetadata(TranslatorInterface $translator): ThrowableMetadata
    {
        return new ThrowableMetadata(true, $this->getCode(), $this->getMessage());
    }

    public function shouldReport(): bool
    {
        return false;
    }
}
