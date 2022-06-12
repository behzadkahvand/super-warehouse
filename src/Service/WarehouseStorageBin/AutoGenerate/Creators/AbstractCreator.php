<?php

namespace App\Service\WarehouseStorageBin\AutoGenerate\Creators;

use App\Dictionary\StorageBinTypeDictionary;
use App\DTO\WarehouseStorageBinAutoGenerateData;
use App\Entity\WarehouseStorageBin;
use App\Repository\WarehouseStorageBinRepository;
use App\Service\WarehouseStorageBin\AutoGenerate\BinDataSetter;
use App\Service\WarehouseStorageBin\AutoGenerate\BinFactory;
use App\Service\WarehouseStorageBin\AutoGenerate\HelperTrait;
use App\Service\WarehouseStorageBin\AutoGenerate\Iterators\BinIteratorFactory;
use App\Service\WarehouseStorageBin\Exceptions\ParentNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractCreator implements CreatorInterface
{
    use HelperTrait;

    public function __construct(
        protected BinFactory $binFactory,
        protected BinDataSetter $binDataSetter,
        protected EntityManagerInterface $manager,
        protected BinIteratorFactory $binIteratorFactory,
        private WarehouseStorageBinRepository $binRepository
    ) {
    }

    public function create(WarehouseStorageBinAutoGenerateData $data): array
    {
        $result                    = [];
        $allIteratorSerials        = $this->binIteratorFactory->createIterator($this->getType(), $data)
                                                              ->toArray();
        $allIteratorSerials        = array_map(fn(string $serial) => $this->formatSerial(
            $serial,
            $data->getWarehouse(),
            $data->getWarehouseStorageArea()
        ), $allIteratorSerials);
        $serialsExistInDatabase    = $this->binRepository->getSerials($allIteratorSerials);
        $serialsNotExistInDatabase = array_diff($allIteratorSerials, $serialsExistInDatabase);

        foreach ($serialsNotExistInDatabase as $formattedSerial) {
            $binObject = $this->binFactory->make();

            $this->binDataSetter->setData($binObject, $data);

            $binObject->setType($this->getType())
                      ->setSerial($formattedSerial)
                      ->setParent($this->findParent($formattedSerial));

            $this->manager->persist($binObject);

            array_push($result, $binObject);
        }

        return $result;
    }

    protected function findParent(
        string $formattedSerial,
    ): null|WarehouseStorageBin|ParentNotFoundException {
        $formattedParentSerial = $this->getFormattedParentSerial($formattedSerial);

        $parent = $this->findInDatabase($formattedParentSerial) ?? $this->findInCache($formattedParentSerial);

        if (empty($parent) && $this->getType() !== StorageBinTypeDictionary::AISLE) {
            throw new ParentNotFoundException();
        }

        return $parent;
    }

    protected function findInCache(string $serial): ?WarehouseStorageBin
    {
        $entities = $this->manager->getUnitOfWork()->getScheduledEntityInsertions();

        $conditionClosure = fn($entity) => $entity instanceof WarehouseStorageBin && $entity->getSerial() === $serial;

        return collect($entities)->first($conditionClosure);
    }

    protected function findInDatabase(string $serial): ?WarehouseStorageBin
    {
        return $this->binRepository->findOneBy([
            'serial' => $serial,
        ]);
    }

    abstract protected function getType(): string;

    abstract protected function getFormattedParentSerial(string $formattedSerial): string;
}