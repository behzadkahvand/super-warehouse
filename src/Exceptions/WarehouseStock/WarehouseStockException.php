<?php

namespace App\Exceptions\WarehouseStock;

use App\Service\ExceptionHandler\ReportableThrowableInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class WarehouseStockException extends Exception implements ReportableThrowableInterface
{
    protected $code = Response::HTTP_BAD_REQUEST;

    public function shouldReport(): bool
    {
        return false;
    }
}
