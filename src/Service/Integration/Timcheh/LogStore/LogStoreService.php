<?php

namespace App\Service\Integration\Timcheh\LogStore;

use DateTimeInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

class LogStoreService
{
    public function __construct(protected DocumentManager $documentManager, protected LogStoreFactory $logStoreFactory)
    {
    }

    public function log(
        string $messageId,
        string $status,
        DateTimeInterface $createdAt,
        ?string $resultCode = null,
        ?string $resultMessage = null
    ): void {
        $logStore = $this->logStoreFactory->create();

        $logStore->setMessageId($messageId)
                 ->setStatus($status)
                 ->setResultCode($resultCode)
                 ->setResultMessage($resultMessage)
                 ->setCreatedAt($createdAt);

        $this->documentManager->persist($logStore);
        $this->documentManager->flush();
    }
}
