<?php

namespace App\Service\Relocate\Picking;

use App\Dictionary\CacheSignatureDictionary;
use App\Entity\Admin;
use App\Entity\Inventory;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Repository\PickListRepository;
use App\Service\Relocate\Exceptions\BinRelocateReserveStockLimitException;
use App\Service\Relocate\Exceptions\ItemNotInStorageBinException;
use App\Service\Relocate\Exceptions\ItemRelocateReserveStockLimitException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Security\Core\Security;

class RelocatePickingService
{
    public function __construct(
        private Security $security,
        private CacheItemPoolInterface $cache,
        private PickListRepository $pickListRepository,
        private ItemSerialRepository $itemSerialRepository
    ) {
    }

    public function checkCanRelocateBin(WarehouseStorageBin $storageBin): void
    {
        $reservedStock = (int) $this->pickListRepository->getStorageBinReserveStocksCount($storageBin);

        if (0 !== $reservedStock) {
            throw new BinRelocateReserveStockLimitException();
        }
    }

    public function checkCanRelocateItem(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        if ($itemSerial->getWarehouseStorageBin()->getId() !== $storageBin->getId()) {
            throw new ItemNotInStorageBinException();
        }

        $hasStock = $this->checkHasStockToRelocate($itemSerial, $storageBin);
        if (!$hasStock) {
            throw new ItemRelocateReserveStockLimitException();
        }
    }

    protected function checkHasStockToRelocate(ItemSerial $itemSerial, WarehouseStorageBin $storageBin): bool
    {
        /** @var Admin $locator */
        $locator = $this->security->getUser();

        $inventory = $itemSerial->getInventory();

        [$cacheItem, $relocateInventoryCount] = $this->getRelocatedInventoryCount($locator, $storageBin, $inventory);

        $allInventoryStock = (int) $this->itemSerialRepository->getItemSerialsCountByInventoryInSpecificBin(
            $inventory,
            $storageBin
        );

        $reservedStock = (int) $this->pickListRepository->getReserveStocksCountForInventoryInSpecificBin(
            $inventory,
            $storageBin
        );

        if (++$relocateInventoryCount > ($allInventoryStock - $reservedStock)) {
            return false;
        }

        $this->saveToCache($cacheItem, $relocateInventoryCount);

        return true;
    }

    private function getRelocatedInventoryCount(
        Admin $locator,
        WarehouseStorageBin $storageBin,
        Inventory $inventory
    ): array {
        $cacheItem = $this->cache->getItem(CacheSignatureDictionary::makeSignature(
            CacheSignatureDictionary::RELOCATE_PICKING_INVENTORY_COUNT,
            $locator->getId(),
            $storageBin->getId(),
            $inventory->getId()
        ));

        $relocateInventoryCount = 0;
        if ($cacheItem->isHit()) {
            $relocateInventoryCount = (int) $cacheItem->get();
        }

        return [$cacheItem, $relocateInventoryCount];
    }

    private function saveToCache(CacheItemInterface $cacheItem, int $relocateInventoryCount): void
    {
        $this->cache->save($cacheItem->set($relocateInventoryCount)->expiresAfter(3 * 60 * 60));
    }
}
