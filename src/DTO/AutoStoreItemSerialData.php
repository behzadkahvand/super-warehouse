<?php

namespace App\DTO;

use App\Entity\ItemBatch;

class AutoStoreItemSerialData
{
    private ?ItemBatch $itemBatch;

    public function getItemBatch(): ?ItemBatch
    {
        return $this->itemBatch;
    }

    public function setItemBatch(?ItemBatch $itemBatch): void
    {
        $this->itemBatch = $itemBatch;
    }
}
