<?php

namespace App\Service\PickList;

use App\Entity\PickList;

final class PickListFactory
{
    public function create(): PickList
    {
        return new PickList();
    }
}
