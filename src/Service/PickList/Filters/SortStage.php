<?php

namespace App\Service\PickList\Filters;

use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;
use App\Service\Warehouse\WarehousePickingStrategyService;

class SortStage implements TagAwarePipelineStageInterface
{
    public function __construct(private WarehousePickingStrategyService $pickingStrategyService)
    {
    }

    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        $result = $this->pickingStrategyService->apply($payload->getWarehouse(), $payload->getResult());

        return $payload->setResult($result);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pick_list.create.item';
    }

    public static function getPriority(): int
    {
        return 10;
    }
}
