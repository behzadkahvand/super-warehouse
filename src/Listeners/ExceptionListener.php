<?php

namespace App\Listeners;

use App\Service\ExceptionHandler\MetadataLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        protected MetadataLoader $metadataLoader,
        protected bool $isExceptionHandlerEnable = true
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->isExceptionHandlerEnable) {
            return;
        }

        $throwable = $event->getThrowable();

        if ($throwable instanceof HttpExceptionInterface) {
            return;
        }

        $metadata = $this->metadataLoader->getMetadata($throwable);

        if ($metadata->isVisibleForUsers()) {
            $statusCode = $metadata->getStatusCode();
            $message    = $metadata->getTitle();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $message    = Response::$statusTexts[$statusCode];
        }

        $event->setThrowable(
            new HttpException($statusCode, $message, $throwable, [], $throwable->getCode())
        );
    }
}
