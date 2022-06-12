<?php

namespace App\Messaging\Handlers\Event\Integration\Timcheh\ShipmentItem;

use App\Messaging\Messages\Event\Integration\Timcheh\OrderItem\UpdateOrderItemInSuperWarehouseMessage;
use App\Service\Shipment\Integration\ShipmentItemUpdateService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateShipmentItemHandler implements MessageHandlerInterface
{
    public function __construct(private ShipmentItemUpdateService $service)
    {
    }

    public function __invoke(UpdateOrderItemInSuperWarehouseMessage $message): void
    {
        $this->service->update($message->getId(), $message->getQuantity());
    }
}
