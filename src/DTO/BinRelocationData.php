<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\WarehouseStorageBin;

final class BinRelocationData
{
    /**
     * @Assert\NotBlank(groups={"bin.relocation.pick","bin.relocation.stow",})
     */
    private ?WarehouseStorageBin $sourceStorageBin = null;

    /**
     * @Assert\NotBlank(groups={"bin.relocation.stow",})
     */
    private ?WarehouseStorageBin $destinationStorageBin = null;


    public function setSourceStorageBin(?WarehouseStorageBin $sourceStorageBin): self
    {
        $this->sourceStorageBin = $sourceStorageBin;

        return $this;
    }

    public function getSourceStorageBin(): ?WarehouseStorageBin
    {
        return $this->sourceStorageBin;
    }

    public function setDestinationStorageBin(?WarehouseStorageBin $destinationStorageBin): self
    {
        $this->destinationStorageBin = $destinationStorageBin;

        return $this;
    }

    public function getDestinationStorageBin(): ?WarehouseStorageBin
    {
        return $this->destinationStorageBin;
    }
}
