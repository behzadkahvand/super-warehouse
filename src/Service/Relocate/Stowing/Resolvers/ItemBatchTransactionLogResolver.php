<?php

namespace App\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\Relocate\Stowing\RelocateItemResolverInterface;
use App\Service\Relocate\Stowing\RelocationItemBatchLogService;

class ItemBatchTransactionLogResolver implements RelocateItemResolverInterface
{
    public function __construct(private RelocationItemBatchLogService $batchLogService)
    {
    }

    public function resolve(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $this->batchLogService->makeItemRelocateBatchLog($storageBin, $itemSerial->getItemBatch());
    }

    public static function getPriority(): int
    {
        return 7;
    }
}
