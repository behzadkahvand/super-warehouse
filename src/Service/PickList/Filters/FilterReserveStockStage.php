<?php

namespace App\Service\PickList\Filters;

use App\Entity\PickList;
use App\Repository\PickListRepository;
use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;
use Tightenco\Collect\Support\Collection;

class FilterReserveStockStage implements TagAwarePipelineStageInterface
{
    public function __construct(private PickListRepository $pickListRepository)
    {
    }

    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        /** @var Collection $result */
        $result = $payload->getResult();

        if ($result->isEmpty()) {
            return $payload;
        }

        $inventory = $payload->getInventory();

        $pickLists = $this->pickListRepository->findPickListsForInventoryWithNotCloseStatus($inventory);

        $finalResult = collect();

        /** @var PickList $pickList */
        foreach ($pickLists as $pickList) {
            $condition = fn($item) => $item[0]->getWarehouseStorageBin()->getId() === $pickList->getStorageBin()->getId();
            $item    = $result->first($condition);
            if (empty($item)) {
                continue;
            }
            $item['total'] -= $pickList->getQuantity();
            $finalResult->add($item);
            $result = $result->reject($condition);
        }

        $finalResult = $finalResult->merge($result->toArray())->filter(fn($item) => $item['total'] > 0);

        return $payload->setResult($finalResult);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pick_list.create.item';
    }

    public static function getPriority(): int
    {
        return 20;
    }
}
