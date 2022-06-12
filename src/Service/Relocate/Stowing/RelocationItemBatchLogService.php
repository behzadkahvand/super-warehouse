<?php

namespace App\Service\Relocate\Stowing;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Document\ItemBatchTransaction;
use App\Entity\Admin;
use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemBatchTransactionRepository;
use App\Service\ItemsTransaction\ItemBatchTransactionLogService;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Security;

class RelocationItemBatchLogService
{
    public function __construct(
        private Security $security,
        private ItemBatchTransactionRepository $batchTransactionRepository,
        private ItemBatchTransactionLogService $transactionLogService
    ) {
    }

    public function makeItemRelocateBatchLog(WarehouseStorageBin $storageBin, ItemBatch $itemBatch): void
    {
        /** @var Admin $locator */
        $locator = $this->security->getUser();

        $itemBatchLog = $this->batchTransactionRepository->findItemBatchRelocated(
            $locator->getId(),
            $storageBin->getId(),
            $itemBatch->getId()
        );

        if (!$itemBatchLog) {
            $itemBatchLog = $this->makeNewItemBatchLog($storageBin, $locator->getId(), $itemBatch->getId(), 0);
        }

        $itemBatchLog->setQuantity($itemBatchLog->getQuantity() + 1)
                     ->setUpdatedAt(new DateTime());
    }

    public function makeBinRelocateBatchLog(WarehouseStorageBin $destinationBin, Collection $relocatedItems): void
    {
        /** @var Admin $locator */
        $locator = $this->security->getUser();

        $batchItemsCount = $this->getBatchItemsCount($relocatedItems);

        foreach ($batchItemsCount as $batchId => $count) {
            $this->makeNewItemBatchLog($destinationBin, $locator->getId(), $batchId, $count);
        }
    }

    protected function makeNewItemBatchLog(
        WarehouseStorageBin $storageBin,
        int $locatorId,
        int $itemBatchId,
        int $quantity
    ): ItemBatchTransaction {
        return $this->transactionLogService->log(
            ItemTransactionActionTypeDictionary::RELOCATE,
            null,
            $storageBin->getWarehouse()->getId(),
            $storageBin->getId(),
            $itemBatchId,
            $quantity,
            $locatorId,
            new DateTime(),
            new DateTime()
        );
    }

    private function getBatchItemsCount(Collection $relocatedItems): array
    {
        $batchLogsCount = [];

        /** @var ItemSerial $item */
        foreach ($relocatedItems as $item) {
            $batchId                  = $item->getItemBatch()->getId();
            $batchLogsCount[$batchId] = ($batchLogsCount[$batchId] ?? 0) + 1;
        }

        return $batchLogsCount;
    }
}
