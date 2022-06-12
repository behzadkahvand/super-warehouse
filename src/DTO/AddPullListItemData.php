<?php

namespace App\DTO;

use App\Entity\PullList;
use Doctrine\Common\Collections\ArrayCollection;

class AddPullListItemData
{
    protected ArrayCollection $receiptItems;

    protected PullList $pullList;

    public function getReceiptItems(): ArrayCollection
    {
        return $this->receiptItems;
    }

    public function setReceiptItems(ArrayCollection $receiptItems): self
    {
        $this->receiptItems = $receiptItems;
        return $this;
    }

    /**
     * @return PullList
     */
    public function getPullList(): PullList
    {
        return $this->pullList;
    }

    /**
     * @param PullList $pullList
     * @return AddPullListItemData
     */
    public function setPullList(PullList $pullList): AddPullListItemData
    {
        $this->pullList = $pullList;
        return $this;
    }
}
