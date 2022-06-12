<?php

namespace App\Service\PickList\HandHeld\Picking\Resolvers;

use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Entity\ReceiptItemBatch;
use App\Entity\ReceiptItemSerial;
use App\Service\ItemBatch\ReceiptItemBatchFactory;
use App\Service\ItemSerial\ReceiptItemSerialFactory;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;
use Doctrine\ORM\EntityManagerInterface;

class InsertReceiptItemSerialAndBatchResolver implements PickingResolverInterface
{
    public function __construct(
        private ReceiptItemSerialFactory $receiptItemSerialFactory,
        private ReceiptItemBatchFactory $receiptItemBatchFactory,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function resolve(PickList $pickList, ItemSerial $itemSerial): void
    {
        $receiptItemSerial = $this->insertReceiptItemSerial($pickList, $itemSerial);

        $receiptItemBatch = $this->insertReceiptItemBatch($pickList, $itemSerial->getItemBatch());

        $this->entityManager->persist($receiptItemSerial);
        $this->entityManager->persist($receiptItemBatch);
        $this->entityManager->flush();
    }

    public static function getPriority(): int
    {
        return 10;
    }

    private function insertReceiptItemSerial(PickList $pickList, ItemSerial $itemSerial): ReceiptItemSerial
    {
        $receiptItemSerial = $this->receiptItemSerialFactory->create();
        $receiptItemSerial->setReceiptItem($pickList->getReceiptItem());
        $receiptItemSerial->setItemSerial($itemSerial);

        return $receiptItemSerial;
    }

    private function insertReceiptItemBatch(PickList $pickList, ItemBatch $itemBatch): ReceiptItemBatch
    {
        $receiptItemBatch = $this->receiptItemBatchFactory->create();
        $receiptItemBatch->setReceiptItem($pickList->getReceiptItem());
        $receiptItemBatch->setItemBatch($itemBatch);

        return $receiptItemBatch;
    }
}
