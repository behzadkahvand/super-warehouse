<?php

namespace App\Service\Integration\Timcheh\LogStore;

use App\Dictionary\LogStoreStatusDictionary;
use App\Messaging\Stamps\Event\UniqueIdStamp;
use DateTime;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\SentStamp;

class SentLogResolver implements LogStoreInterface
{
    public function __construct(private LogStoreService $logStoreService)
    {
    }

    public function support(Envelope $envelope): bool
    {
        return null !== $envelope->last(SentStamp::class);
    }

    public function handleLog(Envelope $envelope): void
    {
        /** @var UniqueIdStamp $uniqueIdStamp */
        $uniqueIdStamp = $envelope->last(UniqueIdStamp::class);

        $this->logStoreService->log($uniqueIdStamp->getMessageId(), LogStoreStatusDictionary::SENT, new DateTime());
    }

    public static function getPriority(): int
    {
        return 1;
    }
}
