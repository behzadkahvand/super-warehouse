<?php

namespace App\Messaging\Handlers\Event\Integration\Timcheh\Shipment;

use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\UpdateOrderShipmentMessage;
use App\Service\Shipment\DTO\ShipmentData;
use App\Service\Shipment\ShipmentUpsertService;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateShipmentHandler implements MessageHandlerInterface
{
    public function __construct(private ShipmentUpsertService $service)
    {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function __invoke(UpdateOrderShipmentMessage $message): void
    {
        $this->service->update(new ShipmentData($message->toArray()));
    }
}
