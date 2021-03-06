<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Editors;

use App\DTO\WarehouseStorageBinAutoGenerateData;
use App\Entity\WarehouseStorageBin;
use App\Repository\WarehouseStorageBinRepository;
use App\Service\WarehouseStorageBin\AutoGenerate\BinDataSetter;
use App\Service\WarehouseStorageBin\AutoGenerate\HelperTrait;
use App\Service\WarehouseStorageBin\AutoGenerate\Iterators\BinIteratorFactory;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractEditor implements EditorInterface
{
    use HelperTrait;

    public function __construct(
        protected BinDataSetter $dataSetter,
        protected EntityManagerInterface $manager,
        protected WarehouseStorageBinRepository $binRepository,
        protected BinIteratorFactory $binIteratorFactory
    ) {
    }

    public function edit(WarehouseStorageBinAutoGenerateData $data): array
    {
        $items = $this->aggregate($data);
        foreach ($items as $item) {
            $this->dataSetter->setData($item, $data);
        }

        $this->manager->flush();

        return $items;
    }

    protected function aggregateFromDatabase(WarehouseStorageBinAutoGenerateData $data): array
    {
        $aggregatedSerials = $this->binIteratorFactory
            ->createIterator($this->getType(), $data)
            ->toArray();

        $formattedAggregatedSerials = array_map(fn(string $serial) => $this->formatSerial(
            $serial,
            $data->getWarehouse(),
            $data->getWarehouseStorageArea()
        ), $aggregatedSerials);

        return $this->binRepository->getBinWithSerialsAndType($formattedAggregatedSerials, $this->getType());
    }

    protected function mergeChildren(array $bins): array
    {
        $result = [];
        /** @var WarehouseStorageBin $bin */
        foreach ($bins as $bin) {
            $result = array_merge($result, $bin->getChildren()->toArray());
        }

        return $result;
    }

    abstract protected function aggregate(WarehouseStorageBinAutoGenerateData $data): array;

    abstract protected function getType(): string;
}
