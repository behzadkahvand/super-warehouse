<?php

namespace App\Service\WarehouseStorageBin\Exceptions;

use Exception;

class ParentNotFoundException extends Exception
{
    protected $message = 'parent not found!';
}
