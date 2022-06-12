<?php

namespace App\Service\Relocate\Stowing\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\ItemsTransaction\ItemSerialTransactionLogService;
use App\Service\Relocate\Stowing\RelocateBinResolverInterface;
use App\Service\Relocate\Stowing\RelocateItemResolverInterface;
use DateTime;
use Symfony\Component\Security\Core\Security;

class ItemSerialTransactionLogResolver implements RelocateItemResolverInterface, RelocateBinResolverInterface
{
    public function __construct(
        private Security $security,
        private ItemSerialTransactionLogService $transactionLogService
    ) {
    }

    public function resolve(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $this->transactionLogService->log(
            ItemTransactionActionTypeDictionary::RELOCATE,
            null,
            $storageBin->getWarehouse()->getId(),
            $storageBin->getId(),
            $itemSerial->getId(),
            $this->security->getUser()->getId(),
            new DateTime()
        );
    }

    public static function getPriority(): int
    {
        return 10;
    }
}
