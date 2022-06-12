<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Exceptions;

use App\Service\ExceptionHandler\RenderableThrowableInterface;
use App\Service\ExceptionHandler\ReportableThrowableInterface;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class BatchSeparationStowingStrategyException extends Exception implements RenderableThrowableInterface, ReportableThrowableInterface
{
    protected $code = Response::HTTP_BAD_REQUEST;

    protected $message = "It's not possible stow this item because of batch separation strategy";

    public function getMetadata(TranslatorInterface $translator): ThrowableMetadata
    {
        return new ThrowableMetadata(true, $this->getCode(), $this->getMessage());
    }

    public function shouldReport(): bool
    {
        return false;
    }
}
