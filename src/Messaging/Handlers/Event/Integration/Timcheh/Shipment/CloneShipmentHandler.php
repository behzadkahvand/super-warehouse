<?php

namespace App\Messaging\Handlers\Event\Integration\Timcheh\Shipment;

use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\CloneOrderShipmentMessage;
use App\Service\Shipment\DTO\ShipmentCloneData;
use App\Service\Shipment\Integration\ShipmentCloneService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CloneShipmentHandler implements MessageHandlerInterface
{
    public function __construct(private ShipmentCloneService $service)
    {
    }

    public function __invoke(CloneOrderShipmentMessage $message): void
    {
        ($this->service)(new ShipmentCloneData($message->toArray()));
    }
}
