<?php

namespace App\Service\Shipment\Integration;

use App\Repository\ShipmentItemRepository;
use App\Service\WarehouseStock\GIUpdateWarehouseStockService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Throwable;

final class ShipmentItemUpdateService
{
    public function __construct(
        private ShipmentItemRepository $repository,
        private EntityManagerInterface $manager,
        private GIUpdateWarehouseStockService $stockService
    ) {
    }

    public function update(int $shipmentItemId, int $newQuantity): void
    {
        $shipmentItem = $this->repository->find($shipmentItemId);
        if (!$shipmentItem) {
            throw new EntityNotFoundException('shipment item not found!');
        }

        $currentQuantity = $shipmentItem->getQuantity();

        $this->manager->beginTransaction();

        try {
            $changedQuantity = abs($currentQuantity - $newQuantity);
            if ($currentQuantity > $newQuantity) {
                $this->stockService->withdrawReserveAndSupplyStock($shipmentItem, $changedQuantity);
            } elseif ($currentQuantity < $newQuantity) {
                $this->stockService->depositReserveAndSupplyStock($shipmentItem, $changedQuantity);
            }

            $shipmentItem->setQuantity($newQuantity);
            $shipmentItem->getReceiptItem()->setQuantity($newQuantity);

            $this->manager->flush();
            $this->manager->commit();
        } catch (Throwable $exception) {
            $this->manager->close();
            $this->manager->rollback();

            throw $exception;
        }
    }
}
