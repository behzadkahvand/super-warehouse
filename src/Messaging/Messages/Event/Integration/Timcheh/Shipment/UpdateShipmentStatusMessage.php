<?php

namespace App\Messaging\Messages\Event\Integration\Timcheh\Shipment;

use App\Messaging\Messages\Event\Integration\AbstractIntegrationMessage;
use App\Messaging\Messages\Event\Integration\IntegrationMessageTrait;
use App\Messaging\Messages\Event\Integration\Timcheh\ProducerAsyncMessageInterface;

final class UpdateShipmentStatusMessage extends AbstractIntegrationMessage implements ProducerAsyncMessageInterface
{
    use IntegrationMessageTrait;

    protected int $id;

    protected string $status;

    public function getMessageType(): string
    {
        return 'UPDATE_SHIPMENT_STATUS';
    }

    public function getEntityId(): ?int
    {
        return $this->getId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
