<?php

namespace App\Service\ExceptionHandler\Factories;

use App\Service\ExceptionHandler\ThrowableMetadata;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

abstract class AbstractMetadataFactory implements ServiceSubscriberInterface
{
    final public function __construct(protected ContainerInterface $container)
    {
    }

    abstract public function __invoke(Throwable $throwable, TranslatorInterface $translator): ThrowableMetadata;

    public static function getSubscribedServices(): array
    {
        return [];
    }
}
