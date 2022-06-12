<?php

namespace App\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\ItemsTransaction\ItemSerialTransactionLogService;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;
use DateTime;
use Symfony\Component\Security\Core\Security;

class ItemSerialTransactionLogResolver implements PickingResolverInterface
{
    public function __construct(
        private ItemSerialTransactionLogService $transactionLogService,
        private Security $security
    ) {
    }

    public function resolve(PickList $pickList, ItemSerial $itemSerial): void
    {
        $this->transactionLogService->log(
            ItemTransactionActionTypeDictionary::PICK,
            $pickList->getReceiptItem()->getReceipt()->getId(),
            $pickList->getWarehouse()->getId(),
            $pickList->getStorageBin()->getId(),
            $itemSerial->getId(),
            $this->security->getUser()->getId(),
            new DateTime()
        );
    }

    public static function getPriority(): int
    {
        return 4;
    }
}
