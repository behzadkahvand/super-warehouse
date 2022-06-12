<?php

namespace App\Messaging\Messages\Event\Integration;

abstract class AbstractIntegrationMessage
{
    public function getMessageName(): string
    {
        return get_class($this);
    }

    final public function toArray(): array
    {
        return object_to_array($this);
    }

    abstract public function getMessageType(): string;

    abstract public function getEntityId(): ?int;
}
