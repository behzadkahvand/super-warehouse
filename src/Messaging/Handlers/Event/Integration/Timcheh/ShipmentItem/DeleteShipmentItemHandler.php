<?php

namespace App\Messaging\Handlers\Event\Integration\Timcheh\ShipmentItem;

use App\Messaging\Messages\Event\Integration\Timcheh\OrderItem\DeleteOrderItemMessage;
use App\Service\Shipment\Integration\ShipmentItemDeleteService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteShipmentItemHandler implements MessageHandlerInterface
{
    public function __construct(private ShipmentItemDeleteService $service)
    {
    }

    public function __invoke(DeleteOrderItemMessage $message): void
    {
        $this->service->delete($message->getEntityId());
    }
}
