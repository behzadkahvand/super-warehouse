<?php

namespace App\Service\PullListItem;

use App\Entity\PullListItem;

class PullListItemFactory
{
    public function getPullListItem(): PullListItem
    {
        return new PullListItem();
    }
}
