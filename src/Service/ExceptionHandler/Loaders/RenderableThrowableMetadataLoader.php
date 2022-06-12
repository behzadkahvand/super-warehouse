<?php

namespace App\Service\ExceptionHandler\Loaders;

use App\Service\ExceptionHandler\RenderableThrowableInterface;
use App\Service\ExceptionHandler\ThrowableMetadata;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class RenderableThrowableMetadataLoader implements MetadataLoaderInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function load(Throwable $throwable): ThrowableMetadata
    {
        return $throwable->getMetadata($this->translator);
    }

    public function supports(Throwable $throwable): bool
    {
        return $throwable instanceof RenderableThrowableInterface;
    }

    public static function getPriority(): int
    {
        return 200;
    }
}
