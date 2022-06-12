<?php

namespace App\Service\Shipment\Integration;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ShipmentItemStockTypeDictionary;
use App\Repository\ShipmentRepository;
use App\Service\StatusTransition\StateTransitionHandlerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Throwable;

class ShipmentConfirmService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ShipmentRepository $shipmentRepository,
        private StateTransitionHandlerService $transitionHandlerService
    ) {
    }

    public function __invoke(int $shipmentId): void
    {
        $shipment = $this->shipmentRepository->find($shipmentId);
        if (!$shipment) {
            throw new EntityNotFoundException('Shipment not found!');
        }

        $this->manager->beginTransaction();
        try {
            foreach ($shipment->getShipmentItems() as $shipmentItem) {
                if ($shipmentItem->getStockType() === ShipmentItemStockTypeDictionary::SALEABLE) {
                    $receiptItemStatus = ReceiptStatusDictionary::APPROVED;
                } else {
                    $receiptItemStatus = ReceiptStatusDictionary::WAITING_FOR_SUPPLY;
                }

                $this->transitionHandlerService->transitState($shipmentItem->getReceiptItem(), $receiptItemStatus);
            }

            $this->manager->flush();
            $this->manager->commit();
        } catch (Throwable $exception) {
            $this->manager->close();
            $this->manager->rollback();

            throw $exception;
        }
    }
}
