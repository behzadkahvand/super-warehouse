<?php

namespace App\Messaging\Middlewares\Event;

use App\Messaging\Messages\Event\Integration\AbstractIntegrationMessage;
use App\Messaging\Messages\Event\Integration\Timcheh\LogStore\AbstractLogLifecycleMessage;
use App\Messaging\Messages\Event\Integration\Timcheh\ProducerAsyncMessageInterface;
use App\Messaging\Stamps\Event\EventStoredStamp;
use App\Messaging\Stamps\Event\UniqueIdStamp;
use App\Service\Integration\Timcheh\EventStore\EventStoreData;
use App\Service\Integration\Timcheh\EventStore\EventStoreService;
use DateTime;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class EventStoreMiddleware implements MiddlewareInterface
{
    public function __construct(private EventStoreService $eventStoreService)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if (
            ($message instanceof AbstractIntegrationMessage) &&
            !($message instanceof AbstractLogLifecycleMessage) &&
            (null === $envelope->last(EventStoredStamp::class))
        ) {
            /** @var UniqueIdStamp $uniqueIdStamp */
            $uniqueIdStamp = $envelope->last(UniqueIdStamp::class);

            $this->eventStoreService->append(
                (new EventStoreData())
                    ->setMessageId($uniqueIdStamp->getMessageId())
                    ->setMessageName($message->getMessageName())
                    ->setSourceServiceName(ProducerAsyncMessageInterface::SOURCE_SERVICE_NAME)
                    ->setPayload($message->toArray())
                    ->setCreatedAt(new DateTime())
            );

            $envelope = $envelope->with(new EventStoredStamp());
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
