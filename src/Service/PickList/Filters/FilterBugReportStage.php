<?php

namespace App\Service\PickList\Filters;

use App\Repository\PickListBugReportRepository;
use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;
use Doctrine\ORM\QueryBuilder;

class FilterBugReportStage implements TagAwarePipelineStageInterface
{
    public function __construct(private PickListBugReportRepository $bugReportRepository)
    {
    }

    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        /** @var QueryBuilder $query */
        $query = $payload->getQueryBuilder();

        $inventory = $payload->getInventory();

        $storageBins = $this->bugReportRepository->findStorageBinsForInventoryWithNotDoneStatus($inventory);

        [$rootAlias] = $query->getRootAliases();
        if (!empty($storageBins)) {
            $query->andWhere($query->expr()->notIn("{$rootAlias}.warehouseStorageBin", ':storageBins'))
                  ->setParameter('storageBins', $storageBins);
        }

        return $payload->setQueryBuilder($query);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pick_list.create.item';
    }

    public static function getPriority(): int
    {
        return 30;
    }
}
