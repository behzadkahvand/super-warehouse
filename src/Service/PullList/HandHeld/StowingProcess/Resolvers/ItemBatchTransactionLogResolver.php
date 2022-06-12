<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Repository\PullListRepository;
use App\Service\ItemsTransaction\ItemBatchTransactionLogService;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;
use DateTime;
use Symfony\Component\Security\Core\Security;

class ItemBatchTransactionLogResolver implements StowingResolverInterface
{
    public function __construct(
        private Security $security,
        private PullListRepository $pullListRepository,
        private ItemBatchTransactionLogService $transactionLogService
    ) {
    }

    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        $receipt = $pullListItem->getReceipt();

        if (ReceiptStatusDictionary::DONE !== $receipt->getStatus()) {
            return;
        }

        $this->transactionLogService->log(
            ItemTransactionActionTypeDictionary::STOW,
            $receipt->getId(),
            $storageBin->getWarehouse()->getId(),
            $storageBin->getId(),
            $itemSerial->getItemBatch()->getId(),
            (int) $this->pullListRepository->getReceiptPullListItemsCount($receipt),
            $this->security->getUser()->getId(),
            new DateTime(),
            new DateTime()
        );
    }

    public static function getPriority(): int
    {
        return 1;
    }
}
