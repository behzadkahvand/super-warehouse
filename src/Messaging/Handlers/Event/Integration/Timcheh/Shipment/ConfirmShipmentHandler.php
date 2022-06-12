<?php

namespace App\Messaging\Handlers\Event\Integration\Timcheh\Shipment;

use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\ConfirmOrderShipmentMessage;
use App\Service\Shipment\Integration\ShipmentConfirmService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ConfirmShipmentHandler implements MessageHandlerInterface
{
    public function __construct(private ShipmentConfirmService $service)
    {
    }

    public function __invoke(ConfirmOrderShipmentMessage $message): void
    {
        ($this->service)($message->getId());
    }
}
