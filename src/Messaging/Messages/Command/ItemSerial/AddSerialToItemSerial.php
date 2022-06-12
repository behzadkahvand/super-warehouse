<?php

namespace App\Messaging\Messages\Command\ItemSerial;

class AddSerialToItemSerial
{
    public function __construct(protected int $itemSerialId)
    {
    }

    public function getItemSerialId(): int
    {
        return $this->itemSerialId;
    }
}
