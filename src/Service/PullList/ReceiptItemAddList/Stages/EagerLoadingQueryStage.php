<?php

namespace App\Service\PullList\ReceiptItemAddList\Stages;

use App\Entity\Inventory;
use App\Entity\Product;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;

class EagerLoadingQueryStage implements TagAwarePipelineStageInterface
{
    public function __construct(private QueryBuilderFilterService $filterService)
    {
    }

    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        $queryBuilder = $payload->getQueryBuilder();

        [$rootAlias] = $queryBuilder->getRootAliases();

        $receiptAlias = $this->filterService::getJoinAlias(ReceiptItem::class, Receipt::class);

        if (null === $inventoryAlias = $this->filterService::getJoinAlias(ReceiptItem::class, Inventory::class)) {
            $queryBuilder->leftJoin("{$rootAlias}.inventory", 'Inventory');

            $inventoryAlias = 'Inventory';
        }

        if (null === $productAlias = $this->filterService::getJoinAlias(Inventory::class, Product::class)) {
            $queryBuilder->leftJoin("{$inventoryAlias}.product", 'Product');

            $productAlias = 'Product';
        }

        $queryBuilder->select("Partial {$rootAlias}.{id, quantity}")
                     ->addSelect("Partial {$receiptAlias}.{id}")
                     ->addSelect("Partial {$inventoryAlias}.{id}")
                     ->addSelect("Partial {$productAlias}.{id}");

        return $payload->setQueryBuilder($queryBuilder);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pull_list.receipt_item.add_list';
    }

    public static function getPriority(): int
    {
        return -1;
    }
}
