<?php

namespace App\Service\PullList\ReceiptItemAddList\Stages;

use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;

class PullListItemExistenceQueryFilterStage implements TagAwarePipelineStageInterface
{
    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        $queryBuilder = $payload->getQueryBuilder();

        [$rootAlias] = $queryBuilder->getRootAliases();

        $queryBuilder->leftJoin("{$rootAlias}.pullListItem", 'PullListItem')
                     ->andWhere('PullListItem.id IS NULL');

        return $payload->setQueryBuilder($queryBuilder);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pull_list.receipt_item.add_list';
    }

    public static function getPriority(): int
    {
        return 10;
    }
}
