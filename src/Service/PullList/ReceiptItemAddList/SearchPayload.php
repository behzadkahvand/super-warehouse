<?php

namespace App\Service\PullList\ReceiptItemAddList;

use App\Service\Pipeline\AbstractPipelinePayload;
use Doctrine\ORM\QueryBuilder;

class SearchPayload extends AbstractPipelinePayload
{
    protected array $filters;

    protected array $sorts;

    protected int $warehouseId;

    protected ?QueryBuilder $queryBuilder = null;

    public function __construct(int $warehouseId, array $filters, array $sorts)
    {
        $this->warehouseId = $warehouseId;
        $this->filters     = $filters;
        $this->sorts       = $sorts;
    }

    public function getWarehouseId(): int
    {
        return $this->warehouseId;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function getSorts(): array
    {
        return $this->sorts;
    }

    public function setSorts(array $sorts): self
    {
        $this->sorts = $sorts;

        return $this;
    }

    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(?QueryBuilder $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }
}
