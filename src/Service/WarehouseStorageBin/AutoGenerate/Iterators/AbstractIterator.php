<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Iterators;

use App\DTO\WarehouseStorageBinAutoGenerateData;
use App\Service\WarehouseStorageBin\AutoGenerate\HelperTrait;

abstract class AbstractIterator implements BinIteratorInterface
{
    use HelperTrait;

    protected string $startValue;

    protected string $endValue;

    protected string $increment;

    protected string $current;

    private WarehouseStorageBinAutoGenerateData $data;

    public function __construct(WarehouseStorageBinAutoGenerateData $data)
    {
        $this->data       = $data;
        $this->startValue = $data->getStartValue();
        $this->endValue   = $data->getEndValue();
        $this->increment  = $data->getIncrement();
    }

    protected function getAisleSectionStartValue(): string
    {
        return (string) explode('-', $this->startValue)[0];
    }

    protected function getAisleSectionEndValue(): string
    {
        return (string) explode('-', $this->endValue)[0];
    }

    protected function getBaySectionStartValue(): string
    {
        return (string) explode('-', $this->startValue)[1];
    }

    protected function getBaySectionEndValue(): string
    {
        return (string) explode('-', $this->endValue)[1];
    }

    protected function getCellSectionStartValue(): string
    {
        return (string) explode('-', $this->startValue)[2];
    }

    protected function getCellSectionEndValue(): string
    {
        return (string) explode('-', $this->endValue)[2];
    }

    protected function getAisleSectionIncrement(): string
    {
        return (int) explode('-', $this->increment)[0];
    }

    protected function getBaySectionIncrement(): string
    {
        return (int) explode('-', $this->increment)[1];
    }

    protected function getCellSectionIncrement(): string
    {
        return (int) explode('-', $this->increment)[2];
    }

    public function toArray(): array
    {
        $aggregatedSerials = [];

        foreach ($this as $serial) {
            $aggregatedSerials[] = $serial;
        }

        return $aggregatedSerials;
    }
}
