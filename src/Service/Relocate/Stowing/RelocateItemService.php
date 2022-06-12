<?php

namespace App\Service\Relocate\Stowing;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class RelocateItemService
{
    public function __construct(
        private iterable $resolvers,
        private EntityManagerInterface $entityManager,
        private DocumentManager $documentManager
    ) {
    }

    public function relocate(WarehouseStorageBin $storageBin, ItemSerial $itemSerial): void
    {
        $this->entityManager->beginTransaction();

        try {
            /** @var RelocateItemResolverInterface $resolver */
            foreach ($this->resolvers as $resolver) {
                $resolver->resolve($storageBin, $itemSerial);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
            $this->documentManager->flush();
        } catch (Exception $exception) {
            $this->entityManager->close();
            $this->entityManager->rollback();

            throw $exception;
        }
    }
}
