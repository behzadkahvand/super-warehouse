<?php

namespace App\Messaging\Messages\Event\Integration\Timcheh\Shipment;

use App\Messaging\Messages\Event\Integration\AbstractIntegrationMessage;
use App\Messaging\Messages\Event\Integration\IntegrationMessageTrait;
use App\Messaging\Messages\Event\Integration\Timcheh\ConsumerAsyncMessageInterface;

final class CloneOrderShipmentMessage extends AbstractIntegrationMessage implements ConsumerAsyncMessageInterface
{
    use IntegrationMessageTrait;

    private ?SourceShipmentData $sourceShipment;

    private ?TargetShipmentData $targetShipment;

    public function getMessageType(): string
    {
        return 'CLONE_ORDER_SHIPMENT';
    }

    public function setSourceShipment(array $sourceShipment): self
    {
        $this->sourceShipment = new SourceShipmentData($sourceShipment);

        return $this;
    }

    public function getSourceShipment(): SourceShipmentData
    {
        return $this->sourceShipment;
    }

    public function setTargetShipment(array $targetShipment): self
    {
        $this->targetShipment = new TargetShipmentData($targetShipment);

        return $this;
    }

    public function getTargetShipment(): TargetShipmentData
    {
        return $this->targetShipment;
    }

    public function getEntityId(): ?int
    {
        return $this->getTargetShipment()?->getId();
    }
}
