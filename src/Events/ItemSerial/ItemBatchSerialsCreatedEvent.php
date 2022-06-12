<?php

namespace App\Events\ItemSerial;

use App\Entity\ItemBatch;
use Symfony\Contracts\EventDispatcher\Event;

final class ItemBatchSerialsCreatedEvent extends Event
{
    public function __construct(private ItemBatch $itemBatch)
    {
    }

    public function getItemBatch(): ItemBatch
    {
        return $this->itemBatch;
    }
}
