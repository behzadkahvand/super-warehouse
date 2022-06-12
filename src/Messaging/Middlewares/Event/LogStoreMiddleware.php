<?php

namespace App\Messaging\Middlewares\Event;

use App\Messaging\Messages\Event\Integration\AbstractIntegrationMessage;
use App\Messaging\Messages\Event\Integration\Timcheh\LogStore\AbstractLogLifecycleMessage;
use App\Service\Integration\Timcheh\LogStore\LogStoreContextService;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class LogStoreMiddleware implements MiddlewareInterface
{
    public function __construct(private LogStoreContextService $logStoreContextService)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        $envelope = $stack->next()->handle($envelope, $stack);

        if (
            ($message instanceof AbstractIntegrationMessage) &&
            !($message instanceof AbstractLogLifecycleMessage)
        ) {
            $this->logStoreContextService->handle($envelope);
        }

        return $envelope;
    }
}
