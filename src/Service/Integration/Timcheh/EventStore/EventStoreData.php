<?php

namespace App\Service\Integration\Timcheh\EventStore;

use App\DTO\BaseDTO;
use DateTimeInterface;

class EventStoreData extends BaseDTO
{
    protected string $messageId;

    protected string $messageName;

    protected string $sourceServiceName;

    protected array $payload;

    protected DateTimeInterface $createdAt;

    public function setMessageId(string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function setMessageName(string $messageName): self
    {
        $this->messageName = $messageName;

        return $this;
    }

    public function getMessageName(): string
    {
        return $this->messageName;
    }

    public function setSourceServiceName(string $sourceServiceName): self
    {
        $this->sourceServiceName = $sourceServiceName;

        return $this;
    }

    public function getSourceServiceName(): string
    {
        return $this->sourceServiceName;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
