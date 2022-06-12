<?php

namespace App\Service\Integration\Timcheh\EventStore;

use Doctrine\ODM\MongoDB\DocumentManager;

class EventStoreService
{
    public function __construct(private DocumentManager $documentManager, private EventStoreFactory $eventStoreFactory)
    {
    }

    public function append(EventStoreData $eventStoreData): void
    {
        $eventStore = $this->eventStoreFactory->create();

        $this->documentManager->persist(
            $eventStore
                ->setMessageId($eventStoreData->getMessageId())
                ->setMessageName($eventStoreData->getMessageName())
                ->setSourceServiceName($eventStoreData->getSourceServiceName())
                ->setPayload($eventStoreData->getPayload())
                ->setCreatedAt($eventStoreData->getCreatedAt())
        );

        $this->documentManager->flush();
    }
}
