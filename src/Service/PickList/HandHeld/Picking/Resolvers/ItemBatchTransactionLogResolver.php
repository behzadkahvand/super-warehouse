<?php

namespace App\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Repository\PickListRepository;
use App\Service\ItemsTransaction\ItemBatchTransactionLogService;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;
use DateTime;
use Symfony\Component\Security\Core\Security;

class ItemBatchTransactionLogResolver implements PickingResolverInterface
{
    public function __construct(
        private Security $security,
        private PickListRepository $pickListRepository,
        private ItemBatchTransactionLogService $transactionLogService
    ) {
    }

    public function resolve(PickList $pickList, ItemSerial $itemSerial): void
    {
        $receipt = $pickList->getReceiptItem()->getReceipt();

        if (ReceiptStatusDictionary::DONE !== $receipt->getStatus()) {
            return;
        }

        $this->transactionLogService->log(
            ItemTransactionActionTypeDictionary::PICK,
            $receipt->getId(),
            $pickList->getWarehouse()->getId(),
            $pickList->getStorageBin()->getId(),
            $itemSerial->getItemBatch()->getId(),
            (int) $this->pickListRepository->getReceiptPickListsCount($receipt),
            $this->security->getUser()->getId(),
            new DateTime(),
            new DateTime()
        );
    }

    public static function getPriority(): int
    {
        return 2;
    }
}
