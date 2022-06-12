<?php

namespace App\Service\PickList\HandHeld\Exceptions;

use App\Service\ExceptionHandler\RenderableThrowableInterface;
use App\Service\ExceptionHandler\ReportableThrowableInterface;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PickListNotPickableException extends Exception implements RenderableThrowableInterface, ReportableThrowableInterface
{
    protected $code = Response::HTTP_BAD_REQUEST;

    protected $message = "PickList is not pickAble for picker!";

    public function getMetadata(TranslatorInterface $translator): ThrowableMetadata
    {
        return new ThrowableMetadata(true, $this->getCode(), $this->getMessage());
    }

    public function shouldReport(): bool
    {
        return false;
    }
}
