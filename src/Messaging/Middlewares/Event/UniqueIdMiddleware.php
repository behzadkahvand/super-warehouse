<?php

namespace App\Messaging\Middlewares\Event;

use App\Messaging\Messages\Event\Integration\AbstractIntegrationMessage;
use App\Messaging\Messages\Event\Integration\Timcheh\LogStore\AbstractLogLifecycleMessage;
use App\Messaging\Messages\Event\Integration\Timcheh\ProducerAsyncMessageInterface;
use App\Messaging\Stamps\Event\UniqueIdStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class UniqueIdMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if (
            ($message instanceof AbstractIntegrationMessage) &&
            !($message instanceof AbstractLogLifecycleMessage) &&
            (null === $envelope->last(UniqueIdStamp::class))
        ) {
            $messageId = $this->generateUniqueId($message);

            $envelope = $envelope->with(new UniqueIdStamp($messageId));
        }

        return $stack->next()->handle($envelope, $stack);
    }

    protected function generateUniqueId(object $message): string
    {
        return ProducerAsyncMessageInterface::SOURCE_SERVICE_NAME . "-" . class_basename($message) . "-" . $message->getEntityId() . "-" . microtime();
    }
}
