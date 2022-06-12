<?php

namespace App\DTO;

use App\Entity\PickList;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class HandHeldAcceptPickListData
{
    /**
     * @var ArrayCollection|PickList[]
     *
     * @Assert\NotBlank
     * @Assert\Count(min=1)
     */
    private $items;

    public function getItems(): ?ArrayCollection
    {
        return $this->items ?? null;
    }

    public function setItems(ArrayCollection $items): self
    {
        $this->items = $items;

        return $this;
    }
}
