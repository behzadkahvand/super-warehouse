<?php

namespace App\Events\PickList;

use App\Entity\Receipt;
use Symfony\Contracts\EventDispatcher\Event;

final class GoodIssuePickListCreatedEvent extends Event implements PickListCreatedEventInterface
{
    public function __construct(private Receipt $receipt)
    {
    }

    public function getReceipt(): Receipt
    {
        return $this->receipt;
    }
}
