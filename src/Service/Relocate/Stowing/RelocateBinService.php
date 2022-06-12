<?php

namespace App\Service\Relocate\Stowing;

use App\Entity\WarehouseStorageBin;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\StorageBinNotActiveForStowException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class RelocateBinService
{
    public function __construct(
        private iterable $resolvers,
        private EntityManagerInterface $entityManager,
        private DocumentManager $documentManager,
        private RelocationItemBatchLogService $batchLogService
    ) {
    }

    public function relocate(WarehouseStorageBin $sourceBin, WarehouseStorageBin $destinationBin): void
    {
        if (!$destinationBin->checkIsActiveForStow()) {
            throw new StorageBinNotActiveForStowException();
        }

        $this->entityManager->beginTransaction();

        try {
            $itemSerials = $sourceBin->getItemSerials();

            foreach ($itemSerials as $itemSerial) {
                /** @var RelocateBinResolverInterface $resolver */
                foreach ($this->resolvers as $resolver) {
                    $resolver->resolve($destinationBin, $itemSerial);
                }

                $this->entityManager->flush();
            }

            $this->batchLogService->makeBinRelocateBatchLog($destinationBin, $itemSerials);

            $this->entityManager->commit();
            $this->documentManager->flush();
        } catch (Exception $exception) {
            $this->entityManager->close();
            $this->entityManager->rollback();

            throw $exception;
        }
    }
}
