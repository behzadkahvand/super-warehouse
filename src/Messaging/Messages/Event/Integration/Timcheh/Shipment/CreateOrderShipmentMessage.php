<?php

namespace App\Messaging\Messages\Event\Integration\Timcheh\Shipment;

use App\Messaging\Messages\Event\Integration\AbstractIntegrationMessage;
use App\Messaging\Messages\Event\Integration\Timcheh\ConsumerAsyncMessageInterface;

final class CreateOrderShipmentMessage extends AbstractIntegrationMessage implements
    ShipmentDataIntegrationInterface,
    ConsumerAsyncMessageInterface
{
    use OrderShipmentMessageDataTrait;

    public function getMessageType(): string
    {
        return 'CREATE_ORDER_SHIPMENT';
    }
}
