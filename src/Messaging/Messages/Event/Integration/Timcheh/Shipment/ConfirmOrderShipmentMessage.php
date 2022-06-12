<?php

namespace App\Messaging\Messages\Event\Integration\Timcheh\Shipment;

use App\Messaging\Messages\Event\Integration\AbstractIntegrationMessage;
use App\Messaging\Messages\Event\Integration\IntegrationMessageTrait;
use App\Messaging\Messages\Event\Integration\Timcheh\ConsumerAsyncMessageInterface;

final class ConfirmOrderShipmentMessage extends AbstractIntegrationMessage implements ConsumerAsyncMessageInterface
{
    use IntegrationMessageTrait;

    private int $id;

    public function getMessageType(): string
    {
        return 'CONFIRM_ORDER_SHIPMENT';
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
}
