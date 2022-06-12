<?php

namespace App\Service\Shipment\Integration;

use App\Repository\ShipmentItemRepository;
use App\Service\WarehouseStock\GICancelWarehouseStockService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Throwable;

final class ShipmentItemDeleteService
{
    public function __construct(
        private ShipmentItemRepository $repository,
        private EntityManagerInterface $manager,
        private GICancelWarehouseStockService $stockService
    ) {
    }

    /**
     * @throws EntityNotFoundException
     * @throws Throwable
     */
    public function delete(int $shipmentItemId): void
    {
        $shipmentItem = $this->repository->find($shipmentItemId);
        if (!$shipmentItem) {
            throw new EntityNotFoundException('shipment item not found!');
        }

        $this->manager->beginTransaction();

        try {
            $this->stockService->withdrawReserveAndSupplyStock($shipmentItem);
            $this->manager->remove($shipmentItem);
            $this->manager->remove($shipmentItem->getReceiptItem());

            $this->manager->flush();
            $this->manager->commit();
        } catch (Throwable $exception) {
            $this->manager->close();
            $this->manager->rollback();

            throw $exception;
        }
    }
}
