<?php

namespace App\Service\MongoFilter\Filters\Integration\EventStore;

use App\Service\MongoFilter\AbstractPipelineFilter;
use App\Service\MongoFilter\FilterPayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;

class PayloadFilter extends AbstractPipelineFilter implements TagAwarePipelineStageInterface
{
    protected function doInvoke(FilterPayload $payload): FilterPayload
    {
        $payloadItems = $payload->getRequestFilters()['filter'][$this->filterName()];
        $builder = $payload->getQueryBuilder();
        foreach ($payloadItems as $key => $value) {
            $builder->field('payload.' . $key)->equals($this->typeHintValue((string)$value));
        }

        return $payload->setQueryBuilder(
            $builder
        );
    }

    protected function filterName(): string
    {
        return "event_store.payload";
    }

    public static function getPriority(): int
    {
        return 50;
    }

    private function typeHintValue(string $value): int|bool|string
    {
        if (is_numeric($value)) {
            $value = (int)$value;
        } elseif ($value === "true" || $value === "false") {
            $value = (bool)$value;
        }

        return $value;
    }
}
