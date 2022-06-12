<?php

namespace App\Events\Receipt;

use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use Symfony\Contracts\EventDispatcher\Event;

final class GRMarketPlacePackageCreatedEvent extends Event
{
    public function __construct(private GRMarketPlacePackageReceipt $receipt)
    {
    }

    public function getReceipt(): GRMarketPlacePackageReceipt
    {
        return $this->receipt;
    }
}
