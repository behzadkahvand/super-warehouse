<?php

namespace App\DTO;

use App\Entity\ItemBatch;

class CustomStoreItemSerialData
{
    private ?ItemBatch $itemBatch;

    private array $serials;

    public function getItemBatch(): ?ItemBatch
    {
        return $this->itemBatch;
    }

    public function setItemBatch(?ItemBatch $itemBatch): void
    {
        $this->itemBatch = $itemBatch;
    }

    public function getSerials(): array
    {
        return $this->serials;
    }

    public function setSerials(array $serials): void
    {
        $this->serials = $serials;
    }
}
