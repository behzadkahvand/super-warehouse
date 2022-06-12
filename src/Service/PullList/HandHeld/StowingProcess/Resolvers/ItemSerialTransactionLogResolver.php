<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Service\ItemsTransaction\ItemSerialTransactionLogService;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;
use DateTime;
use Symfony\Component\Security\Core\Security;

class ItemSerialTransactionLogResolver implements StowingResolverInterface
{
    public function __construct(
        private ItemSerialTransactionLogService $transactionLogService,
        private Security $security
    ) {
    }

    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        $this->transactionLogService->log(
            ItemTransactionActionTypeDictionary::STOW,
            $pullListItem->getReceipt()->getId(),
            $storageBin->getWarehouse()->getId(),
            $storageBin->getId(),
            $itemSerial->getId(),
            $this->security->getUser()->getId(),
            new DateTime()
        );
    }

    public static function getPriority(): int
    {
        return 3;
    }
}
