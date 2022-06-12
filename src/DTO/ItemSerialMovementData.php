<?php

namespace App\DTO;

use App\Entity\ItemSerial;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\WarehouseStorageBin;

final class ItemSerialMovementData
{
    /**
     * @Assert\NotBlank(groups={"handHeld.pullList","item.relocation",})
     */
    private ?WarehouseStorageBin $storageBin = null;

    /**
     * @Assert\NotBlank(groups={"handHeld.pullList.stow","item.relocation",})
     */
    private ?ItemSerial $itemSerial = null;

    public function setStorageBin(?WarehouseStorageBin $storageBin): self
    {
        $this->storageBin = $storageBin;

        return $this;
    }

    public function getStorageBin(): ?WarehouseStorageBin
    {
        return $this->storageBin;
    }

    public function setItemSerial(?ItemSerial $itemSerial): self
    {
        $this->itemSerial = $itemSerial;

        return $this;
    }

    public function getItemSerial(): ?ItemSerial
    {
        return $this->itemSerial;
    }
}
