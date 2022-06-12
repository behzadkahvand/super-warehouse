<?php

namespace App\Service\PickList\Filters;

use App\Repository\ItemSerialRepository;
use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;

class CreateQueryStage implements TagAwarePipelineStageInterface
{
    public function __construct(private ItemSerialRepository $repository)
    {
    }

    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        $query = $this->repository->getItemSerialsWithInventoryQueryBuilder($payload->getInventory());

        return $payload->setQueryBuilder($query);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pick_list.create.item';
    }

    public static function getPriority(): int
    {
        return 40;
    }
}
