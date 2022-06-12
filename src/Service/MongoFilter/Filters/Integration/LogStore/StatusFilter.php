<?php

namespace App\Service\MongoFilter\Filters\Integration\LogStore;

use App\Service\MongoFilter\AbstractPipelineFilter;
use App\Service\MongoFilter\FilterPayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;

class StatusFilter extends AbstractPipelineFilter implements TagAwarePipelineStageInterface
{
    protected function doInvoke(FilterPayload $payload): FilterPayload
    {
        return $payload->setQueryBuilder(
            $payload->getQueryBuilder()->field('status')
                ->equals((string)$payload->getRequestFilters()['filter'][$this->filterName()])
        );
    }

    protected function filterName(): string
    {
        return "log_store.status";
    }

    public static function getPriority(): int
    {
        return 90;
    }
}
