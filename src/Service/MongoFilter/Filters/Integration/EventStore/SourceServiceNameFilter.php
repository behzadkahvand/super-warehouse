<?php

namespace App\Service\MongoFilter\Filters\Integration\EventStore;

use App\Service\MongoFilter\AbstractPipelineFilter;
use App\Service\MongoFilter\FilterPayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;

class SourceServiceNameFilter extends AbstractPipelineFilter implements TagAwarePipelineStageInterface
{
    protected function doInvoke(FilterPayload $payload): FilterPayload
    {
        return $payload->setQueryBuilder(
            $payload->getQueryBuilder()->field('source_service_name')
                ->equals((string)$payload->getRequestFilters()['filter'][$this->filterName()])
        );
    }

    protected function filterName(): string
    {
        return "event_store.sourceServiceName";
    }

    public static function getPriority(): int
    {
        return 80;
    }
}
