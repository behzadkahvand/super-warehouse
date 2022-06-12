<?php

namespace App\Service\PullList\ReceiptItemAddList\Stages;

use App\Dictionary\ReceiptStatusDictionary;
use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;

class DefaultFilterAndSortStage implements TagAwarePipelineStageInterface
{
    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        $filters = $payload->getFilters();
        $sorts   = $payload->getSorts();

        $filters['status'] = ReceiptStatusDictionary::READY_TO_STOW;

        $defaultSorts = ['-id', '-receipt.id'];

        $sorts = array_merge($defaultSorts, $sorts);

        return $payload->setFilters($filters)->setSorts($sorts);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pull_list.receipt_item.add_list';
    }

    public static function getPriority(): int
    {
        return 95;
    }
}
