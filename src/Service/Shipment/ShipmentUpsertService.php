<?php

namespace App\Service\Shipment;

use App\Dictionary\ShipmentStatusDictionary;
use App\DTO\GIShipmentReceiptData;
use App\DTO\Integration\ShipmentItemData;
use App\Entity\Shipment;
use App\Entity\ShipmentItem;
use App\Repository\InventoryRepository;
use App\Repository\ShipmentRepository;
use App\Repository\WarehouseRepository;
use App\Service\Receipt\GIShipmentReceiptService;
use App\Service\Shipment\DTO\ShipmentData;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Throwable;

class ShipmentUpsertService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ShipmentRepository $shipmentRepository,
        private ShipmentFactory $shipmentFactory,
        private ShipmentItemFactory $shipmentItemFactory,
        private InventoryRepository $inventoryRepository,
        private GIShipmentReceiptService $GIShipmentReceiptService,
        private WarehouseRepository $warehouseRepository
    ) {
    }

    public function create(ShipmentData $data): Shipment
    {
        $this->manager->beginTransaction();
        try {
            $shipment = $this->shipmentFactory->create();

            $this->setData($shipment, $data);

            $this->manager->persist($shipment);

            $this->createShipmentItems($data, $shipment);

            $this->createReceipt($shipment, $data);

            $this->manager->flush();
            $this->manager->commit();

            return $shipment;
        } catch (Throwable $exception) {
            $this->manager->close();
            $this->manager->rollback();

            throw $exception;
        }
    }

    public function update(ShipmentData $data): Shipment
    {
        $this->manager->beginTransaction();
        try {
            $shipment = $this->shipmentRepository->find($data->getId());

            if (!$shipment) {
                throw new EntityNotFoundException('Shipment not found!');
            }

            $this->setData($shipment, $data);

            $this->checkIsCanceled($shipment);

            $this->manager->flush();
            $this->manager->commit();

            return $shipment;
        } catch (Throwable $exception) {
            $this->manager->close();
            $this->manager->rollback();

            throw $exception;
        }
    }

    public function updateShipmentItems(Shipment $shipment, array $items): void
    {
        /** @var ShipmentItem $item */
        foreach ($items as $item) {
            $shipment->addShipmentItem($item);

            $item->getReceiptItem()->setReceipt($shipment->getReceipt());
        }
    }

    private function createShipmentItems(ShipmentData $data, Shipment $shipment): void
    {
        /** @var ShipmentItemData $item */
        foreach ($data->getItems() as $item) {
            $inventory = $this->inventoryRepository->find($item->getInventoryId());

            if (!$inventory) {
                throw new EntityNotFoundException('Shipment Item Inventory not found!');
            }

            $shipmentItem = $this->shipmentItemFactory->create()
                                                      ->setId($item->getId())
                                                      ->setInventory($inventory)
                                                      ->setQuantity($item->getQuantity())
                                                      ->setStockType($item->getStockType());

            $shipment->addShipmentItem($shipmentItem);

            $this->manager->persist($shipmentItem);
        }
    }

    private function setData(Shipment $shipment, ShipmentData $data): void
    {
        $shipment->setId($data->getId())
                 ->setDeliveryDate($data->getDeliveryDate())
                 ->setCategory($data->getCategory())
                 ->setStatus($data->getStatus());
    }

    private function createReceipt(Shipment $shipment, ShipmentData $data): void
    {
        $warehouse = $this->warehouseRepository->find($data->getWarehouseId());

        if (!$warehouse) {
            throw new EntityNotFoundException('shipping warehouse not found!');
        }

        $this->GIShipmentReceiptService->create(
            (new GIShipmentReceiptData())
                ->setShipment($shipment)
                ->setWarehouse($warehouse)
        );
    }

    private function checkIsCanceled(Shipment $shipment): void
    {
        if (ShipmentStatusDictionary::CANCELED === $shipment->getStatus()) {
            $this->GIShipmentReceiptService->cancel($shipment);
        }
    }
}
