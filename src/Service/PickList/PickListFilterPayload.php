<?php

namespace App\Service\PickList;

use App\Entity\Inventory;
use App\Entity\Warehouse;
use App\Service\Pipeline\AbstractPipelinePayload;
use Doctrine\ORM\QueryBuilder;
use Tightenco\Collect\Support\Collection;

class PickListFilterPayload extends AbstractPipelinePayload
{
    protected Inventory $inventory;

    protected Warehouse $warehouse;

    protected ?QueryBuilder $queryBuilder = null;

    protected ?Collection $result = null;

    public function getQueryBuilder(): ?QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(?QueryBuilder $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    public function setResult(Collection $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getResult(): Collection
    {
        if (!$this->result && $this->queryBuilder) {
            $this->result = collect($this->queryBuilder->getQuery()->getResult());
        } elseif (!$this->result && !$this->queryBuilder) {
            $this->result = collect([]);
        }

        return $this->result;
    }

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    public function setInventory(Inventory $inventory): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getWarehouse(): Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }
}
