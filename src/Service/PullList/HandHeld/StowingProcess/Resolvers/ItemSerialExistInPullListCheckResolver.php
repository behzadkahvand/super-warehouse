<?php

namespace App\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemSerialRepository;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\ItemSerialNotInPullListException;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;

class ItemSerialExistInPullListCheckResolver implements StowingResolverInterface
{
    public function __construct(private ItemSerialRepository $itemSerialRepository)
    {
    }

    public function resolve(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        $allPullListSerials = $this->itemSerialRepository->getPullListSerials($pullList);

        $serial = collect($allPullListSerials)->filter(fn($item) => $item->getSerial() === $itemSerial->getSerial());

        if ($serial->isEmpty()) {
            throw new ItemSerialNotInPullListException();
        }
    }

    public static function getPriority(): int
    {
        return 23;
    }
}
