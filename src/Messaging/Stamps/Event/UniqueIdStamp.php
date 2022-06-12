<?php

namespace App\Messaging\Stamps\Event;

use Symfony\Component\Messenger\Stamp\StampInterface;

class UniqueIdStamp implements StampInterface
{
    public function __construct(private string $messageId)
    {
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }
}
