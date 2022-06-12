<?php

namespace App\Service\PullListItem\Exceptions;

final class PullListItemExistenceException extends AddPullListItemException
{
    protected $message = 'Pull list item is already exists for receipt item!';
}
