<?php

namespace App\Service\Shipment\Integration;

use App\Entity\Shipment;
use App\Repository\ShipmentItemRepository;
use App\Repository\ShipmentRepository;
use App\Service\Shipment\DTO\ShipmentCloneData;
use App\Service\Shipment\DTO\ShipmentData;
use App\Service\Shipment\ShipmentUpsertService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Throwable;

class ShipmentCloneService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ShipmentRepository $shipmentRepository,
        private ShipmentItemRepository $shipmentItemRepository,
        private ShipmentUpsertService $shipmentUpsertService
    ) {
    }

    public function __invoke(ShipmentCloneData $cloneData): void
    {
        $sourceShipment = $this->shipmentRepository->find($cloneData->getSourceShipment()->getId());

        if (!$sourceShipment) {
            throw new EntityNotFoundException('Source shipment not found!');
        }

        $this->manager->beginTransaction();
        try {
            $targetShipment = $this->shipmentRepository->find($cloneData->getTargetShipment()->getId());

            if (!$targetShipment) {
                $this->createNewShipmentAndAssignItems($cloneData);
            } else {
                $this->assignNewItems($cloneData, $targetShipment);
            }

            if (!$cloneData->getSourceShipment()->getItems()) {
                $this->manager->remove($sourceShipment);
            }

            $this->manager->flush();
            $this->manager->commit();
        } catch (Throwable $exception) {
            $this->manager->close();
            $this->manager->rollback();

            throw $exception;
        }
    }

    private function createNewShipmentAndAssignItems(ShipmentCloneData $cloneData): void
    {
        $targetShipment = $this->shipmentUpsertService->create(
            (new ShipmentData($cloneData->getTargetShipment()->toArray()))->setItems([])
        );

        $items = $this->findItems(
            array_map(fn($item) => $item->getId(), $cloneData->getTargetShipment()->getItems())
        );

        $this->shipmentUpsertService->updateShipmentItems($targetShipment, $items);
    }

    private function assignNewItems(ShipmentCloneData $cloneData, Shipment $targetShipment): void
    {
        $newAddedItemsIds = array_diff(
            array_map(fn($item) => $item->getId(), $cloneData->getTargetShipment()->getItems()),
            array_map(fn($shipmentItem) => $shipmentItem->getId(), $targetShipment->getShipmentItems()->toArray())
        );

        $items = $this->findItems($newAddedItemsIds);

        $this->shipmentUpsertService->updateShipmentItems($targetShipment, $items);
    }

    private function findItems(array $itemsIds): array
    {
        $shipmentItems = [];
        foreach ($itemsIds as $itemId) {
            $shipmentItem = $this->shipmentItemRepository->find($itemId);

            if (!$shipmentItem) {
                throw new EntityNotFoundException('Shipment item not found!');
            }

            $shipmentItems[] = $shipmentItem;
        }

        return $shipmentItems;
    }
}
