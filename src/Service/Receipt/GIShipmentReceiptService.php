<?php

namespace App\Service\Receipt;

use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\GIShipmentReceiptData;
use App\Entity\Receipt;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\Shipment;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Service\WarehouseStock\GIAndSTOutboundWarehouseStockService;
use App\Service\WarehouseStock\GICancelWarehouseStockService;
use Doctrine\ORM\EntityManagerInterface;

class GIShipmentReceiptService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ReceiptItemFactory $receiptItemFactory,
        private ReceiptFactory $receiptFactory,
        private GIAndSTOutboundWarehouseStockService $GIAndSTOutboundWarehouseStockService,
        private StateTransitionHandlerService $transitionHandlerService,
        private GICancelWarehouseStockService $GICancelWarehouseStockService
    ) {
    }

    public function create(GIShipmentReceiptData $receiptData): Receipt
    {
        /** @var GIShipmentReceipt $receipt */
        $receipt = $this->receiptFactory->create(ReceiptReferenceTypeDictionary::GI_SHIPMENT);

        $receipt->setSourceWarehouse($receiptData->getWarehouse())
                ->setReference($receiptData->getShipment())
                ->setStatus(ReceiptStatusDictionary::RESERVED);

        $this->manager->persist($receipt);

        $this->createReceiptItems($receipt);

        $this->manager->flush();

        return $receipt;
    }


    public function cancel(Shipment $shipment): void
    {
        foreach ($shipment->getShipmentItems() as $shipmentItem) {
            $this->transitionHandlerService->transitState(
                $shipmentItem->getReceiptItem(),
                ReceiptStatusDictionary::CANCELED
            );

            $this->GICancelWarehouseStockService->withdrawReserveAndSupplyStock($shipmentItem);
        }
    }

    private function createReceiptItems(GIShipmentReceipt $receipt): void
    {
        $shipment = $receipt->getReference();

        foreach ($shipment->getShipmentItems() as $shipmentItem) {
            $receiptItem = $this->receiptItemFactory->create();

            $receiptItem->setQuantity($shipmentItem->getQuantity())
                        ->setInventory($shipmentItem->getInventory())
                        ->setStatus(ReceiptStatusDictionary::RESERVED);

            $receipt->addReceiptItem($receiptItem);

            $this->manager->persist($receiptItem);

            $this->GIAndSTOutboundWarehouseStockService->depositReservedAndSupplyStock($receiptItem);

            $shipmentItem->setReceiptItem($receiptItem);
        }
    }
}
