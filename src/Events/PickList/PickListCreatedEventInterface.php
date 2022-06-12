<?php

namespace App\Events\PickList;

use App\Entity\Receipt;

interface PickListCreatedEventInterface
{
    public function getReceipt(): Receipt;
}
