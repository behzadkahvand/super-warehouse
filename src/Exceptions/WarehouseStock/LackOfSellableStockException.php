<?php

namespace App\Exceptions\WarehouseStock;

final class LackOfSellableStockException extends WarehouseStockException
{
    protected $message = "Warehouse sellable stock is less than shipping quantity!";
}
