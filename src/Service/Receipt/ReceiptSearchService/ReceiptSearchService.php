<?php

namespace App\Service\Receipt\ReceiptSearchService;

use App\Service\ORM\QueryBuilderFilterService;
use Doctrine\ORM\QueryBuilder;

class ReceiptSearchService
{
    public function __construct(
        private QueryBuilderFilterService $filterService,
        private ReceiptSearchFactory $factory
    ) {
    }

    public function perform(array $data): QueryBuilder
    {
        $filter              = $data['filter'] ?? [];
        $receiptType         = $filter['type'] ?? null;
        $referenceIsFiltered = isset($filter['reference.id']);

        $receiptClass = $this->factory->getResourceReceiptClass($referenceIsFiltered, $receiptType);

        return $this->filterService->filter($receiptClass, $data);
    }
}
