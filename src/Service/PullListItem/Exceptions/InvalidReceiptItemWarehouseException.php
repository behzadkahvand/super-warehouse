<?php

namespace App\Service\PullListItem\Exceptions;

final class InvalidReceiptItemWarehouseException extends AddPullListItemException
{
    protected $message = 'Receipt item warehouse must be equals to pull list warehouse!';
}
