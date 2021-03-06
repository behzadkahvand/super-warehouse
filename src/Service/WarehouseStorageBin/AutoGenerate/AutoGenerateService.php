<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate;

use App\DTO\WarehouseStorageBinAutoGenerateData;
use App\Service\WarehouseStorageBin\AutoGenerate\Handlers\HandlerInterface;

class AutoGenerateService
{
    public function __construct(private iterable $handlers)
    {
    }

    public function perform(WarehouseStorageBinAutoGenerateData $data): array
    {
        /** @var HandlerInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->supports($data)) {
                return $handler->handle($data);
            }
        }
    }
}
