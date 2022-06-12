<?php

namespace App\Service\PullList\ReceiptItemAddList\Stages;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\Pipeline\AbstractPipelinePayload;
use App\Service\Pipeline\TagAwarePipelineStageInterface;

class WarehouseQueryFilterStage implements TagAwarePipelineStageInterface
{
    public function __construct(private QueryBuilderFilterService $filterService)
    {
    }

    public function __invoke(AbstractPipelinePayload $payload): AbstractPipelinePayload
    {
        $queryBuilder = $payload->getQueryBuilder();
        $warehouseId  = $payload->getWarehouseId();

        [$rootAlias] = $queryBuilder->getRootAliases();

        $receiptAlias = $this->filterService::getJoinAlias(ReceiptItem::class, Receipt::class);

        if (!$receiptAlias) {
            $receiptAlias = 'Receipt';

            $queryBuilder->innerJoin("{$rootAlias}.receipt", $receiptAlias);
        }

        $queryBuilder
            ->andWhere(
                sprintf(
                    '(%1$s.type = :GRType AND IDENTITY(%1$s.sourceWarehouse) = %2$d) OR 
                    (%1$s.type = :STType AND IDENTITY(%1$s.destinationWarehouse) = %2$d)',
                    $receiptAlias,
                    $warehouseId,
                )
            )
            ->setParameter('GRType', ReceiptTypeDictionary::GOOD_RECEIPT)
            ->setParameter('STType', ReceiptTypeDictionary::STOCK_TRANSFER);

        return $payload->setQueryBuilder($queryBuilder);
    }

    public static function getTag(): string
    {
        return 'app.pipeline_stage.pull_list.receipt_item.add_list';
    }

    public static function getPriority(): int
    {
        return 15;
    }
}
