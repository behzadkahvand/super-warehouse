<?php

use App\Exceptions\WarehouseStock\WarehouseStockException;
use App\Service\ExceptionHandler\ThrowableMetadata;
use App\Service\PullList\ConfirmedPullListByLocator\Exceptions\ConfirmedPullListByLocatorException;
use App\Service\PullList\SentPullListToLocator\Exceptions\SentPullListToLocatorException;
use App\Service\PullListItem\Exceptions\AddPullListItemException;

return [
    WarehouseStockException::class  => function (Throwable $throwable) {
        return new ThrowableMetadata(true, $throwable->getCode(), $throwable->getMessage());
    },
    AddPullListItemException::class => function (Throwable $throwable) {
        return new ThrowableMetadata(true, $throwable->getCode(), $throwable->getMessage());
    },
    SentPullListToLocatorException::class => function (Throwable $throwable) {
        return new ThrowableMetadata(true, $throwable->getCode(), $throwable->getMessage());
    },
    ConfirmedPullListByLocatorException::class => function (Throwable $throwable) {
        return new ThrowableMetadata(true, $throwable->getCode(), $throwable->getMessage());
    },
];
