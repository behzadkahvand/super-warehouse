<?php

namespace App\Service\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Receipt\STInboundReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use App\Entity\ReceiptItem;
use App\Service\ItemBatch\ReceiptItemBatchFactory;
use App\Service\ItemSerial\ReceiptItemSerialFactory;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class STInboundReceiptService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private STInboundReceiptFactory $receiptFactory,
        private ReceiptItemFactory $receiptItemFactory,
        private ReceiptItemSerialFactory $receiptItemSerialFactory,
        private ReceiptItemBatchFactory $receiptItemBatchFactory
    ) {
    }

    public function create(STOutboundReceipt $outboundReceipt): STInboundReceipt
    {
        $this->manager->beginTransaction();

        try {
            $inboundReceipt = $this->createReceipt($outboundReceipt);

            foreach ($outboundReceipt->getReceiptItems() as $outboundReceiptItem) {
                $inboundReceiptItem = $this->createReceiptItem($outboundReceiptItem, $inboundReceipt);

                $this->createItemBatches($outboundReceiptItem, $inboundReceiptItem);

                $this->createItemSerials($outboundReceiptItem, $inboundReceiptItem);
            }

            $this->manager->flush();
            $this->manager->commit();

            return $inboundReceipt;
        } catch (Throwable $exception) {
            $this->manager->close();
            $this->manager->rollback();

            throw $exception;
        }
    }

    private function createReceipt(STOutboundReceipt $outboundReceipt): STInboundReceipt
    {
        $inboundReceipt = $this->receiptFactory->create();

        $inboundReceipt->setStatus(ReceiptStatusDictionary::APPROVED)
                       ->setReference($outboundReceipt)
                       ->setSourceWarehouse($outboundReceipt->getSourceWarehouse())
                       ->setDestinationWarehouse($outboundReceipt->getDestinationWarehouse());

        $this->manager->persist($inboundReceipt);

        $outboundReceipt->setInboundReceipt($inboundReceipt);

        return $inboundReceipt;
    }

    private function createReceiptItem(ReceiptItem $outboundReceiptItem, STInboundReceipt $inboundReceipt): ReceiptItem
    {
        $inboundReceiptItem = $this->receiptItemFactory->create();

        $inboundReceiptItem->setInventory($outboundReceiptItem->getInventory())
                           ->setQuantity($outboundReceiptItem->getQuantity())
                           ->setStatus(ReceiptStatusDictionary::APPROVED);

        $this->manager->persist($inboundReceiptItem);

        $inboundReceipt->addReceiptItem($inboundReceiptItem);

        return $inboundReceiptItem;
    }

    private function createItemBatches(ReceiptItem $outboundReceiptItem, ReceiptItem $inboundReceiptItem): void
    {
        foreach ($outboundReceiptItem->getReceiptItemBatches() as $outboundReceiptItemBatch) {
            $inboundReceiptItemBatch = $this->receiptItemBatchFactory->create();

            $inboundReceiptItemBatch->setItemBatch($outboundReceiptItemBatch->getItemBatch());

            $this->manager->persist($inboundReceiptItemBatch);

            $inboundReceiptItem->addReceiptItemBatch($inboundReceiptItemBatch);
        }
    }

    private function createItemSerials(ReceiptItem $outboundReceiptItem, ReceiptItem $inboundReceiptItem): void
    {
        foreach ($outboundReceiptItem->getReceiptItemSerials() as $outboundReceiptItemSerial) {
            $inboundReceiptItemSerial = $this->receiptItemSerialFactory->create();

            $inboundReceiptItemSerial->setItemSerial($outboundReceiptItemSerial->getItemSerial());

            $this->manager->persist($inboundReceiptItemSerial);

            $inboundReceiptItem->addReceiptItemSerial($inboundReceiptItemSerial);
        }
    }
}
