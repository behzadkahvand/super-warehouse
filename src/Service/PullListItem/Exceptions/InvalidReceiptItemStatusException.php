<?php

namespace App\Service\PullListItem\Exceptions;

final class InvalidReceiptItemStatusException extends AddPullListItemException
{
    protected $message = 'Only receipt item with READY_TO_STOW status allowed!';
}
