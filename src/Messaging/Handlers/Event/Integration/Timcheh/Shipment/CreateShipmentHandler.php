<?php

namespace App\Messaging\Handlers\Event\Integration\Timcheh\Shipment;

use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\CreateOrderShipmentMessage;
use App\Service\Shipment\DTO\ShipmentData;
use App\Service\Shipment\ShipmentUpsertService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateShipmentHandler implements MessageHandlerInterface
{
    public function __construct(private ShipmentUpsertService $service)
    {
    }

    public function __invoke(CreateOrderShipmentMessage $message): void
    {
        $this->service->create(new ShipmentData($message->toArray()));
    }
}
