<?php

namespace App\Service\PickList;

use App\DTO\ShipmentPickListData;
use App\Entity\Shipment;
use App\Messaging\Messages\Command\PickList\CreatePickListMessage;
use App\Repository\ShipmentRepository;
use Symfony\Component\Messenger\MessageBusInterface;

final class ShipmentPickListService
{
    public function __construct(
        private ShipmentRepository $shipmentRepository,
        private MessageBusInterface $messageBus
    ) {
    }

    public function create(ShipmentPickListData $data): void
    {
        $shipments = $this->shipmentRepository->getSpecificQuantityOfShipmentsWithReadyToPickReceiptWithinDeliveryRange(
            $data->getPromiseDateFrom(),
            $data->getPromiseDateTo(),
            $data->getQuantity()
        );

        /** @var Shipment $shipment */
        foreach ($shipments as $shipment) {
            $this->messageBus->dispatch(async_message(new CreatePickListMessage($shipment->getId())));
        }
    }
}
