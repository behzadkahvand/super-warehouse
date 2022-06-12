<?php

namespace App\Service\PullList\ReceiptItemAddList\Stages;

use App\Entity\ReceiptItem;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;

class CreateSearchQueryStage implements TagAwarePipelineStageInterface
{
    public function __construct(private QueryBuilderFilterService $filterService)
    {
    }

    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        $queryBuilder = $this->filterService->filter(
            ReceiptItem::class,
            [
                'filter' => $payload->getFilters(),
                'sort'   => $payload->getSorts(),
            ]
        );

        return $payload->setQueryBuilder($queryBuilder);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pull_list.receipt_item.add_list';
    }

    public static function getPriority(): int
    {
        return 20;
    }
}
