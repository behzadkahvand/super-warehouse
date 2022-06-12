<?php

namespace App\Service\Integration\Timcheh\LogStore;

use App\Dictionary\LogStoreStatusDictionary;
use App\Messaging\Messages\Event\Integration\Timcheh\LogStore\ProduceLogLifecycleMessage;
use App\Messaging\Stamps\Event\UniqueIdStamp;
use DateTime;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;

class ConsumedByWorkerLogResolver implements LogStoreInterface
{
    public function __construct(private MessageBusInterface $eventBus)
    {
    }

    public function support(Envelope $envelope): bool
    {
        return null !== $envelope->last(ConsumedByWorkerStamp::class);
    }

    public function handleLog(Envelope $envelope): void
    {
        /** @var UniqueIdStamp $uniqueIdStamp */
        $uniqueIdStamp = $envelope->last(UniqueIdStamp::class);

        $message = (new ProduceLogLifecycleMessage())
            ->setStatus(LogStoreStatusDictionary::PROCESSED)
            ->setCreatedAt(new DateTime())
            ->setMessageId($uniqueIdStamp->getMessageId());

        $this->eventBus->dispatch($message);
    }

    public static function getPriority(): int
    {
        return 20;
    }
}
