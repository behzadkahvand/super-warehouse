<?php

namespace App\Service\PullList\ReceiptItemAddList\Stages;

use App\Dictionary\ReceiptItemAddListSearchDataMappingDictionary;
use App\Service\ORM\Extension\SortParameterNormalizer;
use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;
use App\Service\PullList\ReceiptItemAddList\Exceptions\SearchDataValidationException;

class FilterAndSortMappingStage implements TagAwarePipelineStageInterface
{
    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        $filters = $this->processFilters($payload->getFilters());
        $sorts   = $this->processSorts($payload->getSorts());

        return $payload->setFilters($filters)->setSorts($sorts);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pull_list.receipt_item.add_list';
    }

    public static function getPriority(): int
    {
        return 100;
    }

    private function processFilters(array $filters): array
    {
        if (empty($filters)) {
            return $filters;
        }

        return array_combine($this->getFilterMappedKeys($filters), array_values($filters));
    }

    private function getFilterMappedKeys(array $filters): array
    {
        return collect($filters)->keys()->map(function ($key) {
            if (!$this->hasMappedFilter($key)) {
                throw new SearchDataValidationException('Receipt Item filters is invalid!');
            }

            return $this->getMappedFilter($key);
        })->toArray();
    }

    private function processSorts(array $sorts): array
    {
        if (empty($sorts)) {
            return $sorts;
        }

        foreach (SortParameterNormalizer::toArray($sorts) as $index => $sort) {
            if (!$this->hasMappedSort($sort['field'])) {
                throw new SearchDataValidationException('Receipt Item sorts is invalid!');
            }

            $sorts[$index] = $sort['direction_prefix'] . $this->getMappedSort($sort['field']);
        }

        return $sorts;
    }

    private function hasMappedFilter(string $filter): bool
    {
        return isset(ReceiptItemAddListSearchDataMappingDictionary::FILTERS[$filter]);
    }

    private function getMappedFilter(string $filter): ?string
    {
        return ReceiptItemAddListSearchDataMappingDictionary::FILTERS[$filter];
    }

    private function hasMappedSort(string $sort): bool
    {
        return isset(ReceiptItemAddListSearchDataMappingDictionary::SORTS[$sort]);
    }

    private function getMappedSort(string $sort): ?string
    {
        return ReceiptItemAddListSearchDataMappingDictionary::SORTS[$sort];
    }
}
