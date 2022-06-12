<?php

namespace App\Service\WarehouseStorageBin\Exceptions;

use Exception;

class BinTypeInvalidException extends Exception
{
    protected $message = 'Bin Type is invalid!';
}
