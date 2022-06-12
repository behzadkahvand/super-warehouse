<?php

namespace App\Service\MongoFilter\Filters\Integration\EventStore;

use App\Service\MongoFilter\AbstractPipelineFilter;
use App\Service\MongoFilter\FilterPayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;
use DateTime;

class MinCreatedAtFilter extends AbstractPipelineFilter implements TagAwarePipelineStageInterface
{
    protected function doInvoke(FilterPayload $payload): FilterPayload
    {
        return $payload->setQueryBuilder(
            $payload->getQueryBuilder()->field('created_at')
                ->gte(new DateTime((string)$payload->getRequestFilters()['filter'][$this->filterName()]))
        );
    }

    protected function filterName(): string
    {
        return "event_store.createdAt.min";
    }

    public static function getPriority(): int
    {
        return 70;
    }
}
