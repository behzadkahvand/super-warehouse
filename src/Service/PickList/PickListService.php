<?php

namespace App\Service\PickList;

use App\Dictionary\PickListPriorityDictionary;
use App\Dictionary\PickListStatusDictionary;
use App\Dictionary\WarehouseTrackingTypeDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Entity\ReceiptItem;
use App\Service\PickList\Exceptions\PickListQuantityException;
use App\Service\Pipeline\Pipeline;
use Doctrine\ORM\EntityManagerInterface;
use Tightenco\Collect\Support\Collection;

class PickListService
{
    public function __construct(
        private iterable $itemSerialFilters,
        private PickListFactory $pickListFactory,
        private EntityManagerInterface $manager
    ) {
    }

    public function create(ReceiptItem $receiptItem, bool $checkQuantity = false): array
    {
        $warehouse = $receiptItem->getReceipt()->getSourceWarehouse();
        if ($warehouse->getTrackingType() !== WarehouseTrackingTypeDictionary::SERIAL) {
            //todo: This section must be replaced with BATCH logic
            return [];
        }
        $payload = new PickListFilterPayload();
        $payload->setInventory($receiptItem->getInventory())
                ->setWarehouse($warehouse);

        $itemSerials = Pipeline::fromStages($this->itemSerialFilters)
                               ->process($payload)
                               ->getResult();

        return $this->createNewPickLists($itemSerials, $receiptItem, $checkQuantity);
    }

    private function createNewPickLists(Collection $itemSerials, ReceiptItem $receiptItem, bool $checkQuantity = false): array
    {
        $quantity = $receiptItem->getRemainedQuantity();

        if ($checkQuantity && $quantity > $itemSerials->sum(fn($item) => $item['total'])) {
            throw new PickListQuantityException();
        }

        $pickLists = [];
        while ($quantity > 0 && !$itemSerials->isEmpty()) {
            $item = $itemSerials->shift();

            $pickList = $this->pickListFactory->create();
            $this->fillPickList($pickList, $receiptItem, $quantity, $item);

            $this->manager->persist($pickList);

            $quantity -= $item['total'];
            $pickLists[] = $pickList;
        }
        $this->manager->flush();

        return $pickLists;
    }

    private function fillPickList(
        PickList $pickList,
        ReceiptItem $receiptItem,
        int $quantity,
        $item
    ): void {
        /** @var ItemSerial $itemSerial */
        $itemSerial          = $item[0];
        $warehouseStorageBin = $itemSerial->getWarehouseStorageBin();
        $pickList->setWarehouse($receiptItem->getReceipt()->getSourceWarehouse())
                 ->setPriority(PickListPriorityDictionary::MEDIUM)
                 ->setStatus(PickListStatusDictionary::WAITING_FOR_ACCEPT)
                 ->setQuantity(min($quantity, $item['total']))
                 ->setStorageArea($warehouseStorageBin->getWarehouseStorageArea())
                 ->setStorageBin($warehouseStorageBin)
                 ->setReceiptItem($receiptItem);
    }
}
