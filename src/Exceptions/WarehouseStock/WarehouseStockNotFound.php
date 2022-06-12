<?php

namespace App\Exceptions\WarehouseStock;

use Symfony\Component\HttpFoundation\Response;

final class WarehouseStockNotFound extends WarehouseStockException
{
    protected $message = "There is no stock for given inventory for your warehouse!";

    protected $code = Response::HTTP_NOT_FOUND;

    public function shouldReport(): bool
    {
        return true;
    }
}
