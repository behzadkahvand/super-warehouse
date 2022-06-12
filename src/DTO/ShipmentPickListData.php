<?php

namespace App\DTO;

use DateTimeInterface;

final class ShipmentPickListData
{
    private int $quantity;
    private DateTimeInterface $promiseDateFrom;
    private DateTimeInterface $promiseDateTo;

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPromiseDateFrom(): DateTimeInterface
    {
        return $this->promiseDateFrom;
    }

    public function setPromiseDateFrom(DateTimeInterface $promiseDateFrom): void
    {
        $this->promiseDateFrom = $promiseDateFrom;
    }

    public function getPromiseDateTo(): DateTimeInterface
    {
        return $this->promiseDateTo;
    }

    public function setPromiseDateTo(DateTimeInterface $promiseDateTo): void
    {
        $this->promiseDateTo = $promiseDateTo;
    }
}
