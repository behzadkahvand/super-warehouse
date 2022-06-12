<?php

namespace App\Exceptions\WarehouseStock;

final class LackOfPhysicalStockException extends WarehouseStockException
{
    protected $message = "Warehouse physical stock is less than shipping quantity!";
}
