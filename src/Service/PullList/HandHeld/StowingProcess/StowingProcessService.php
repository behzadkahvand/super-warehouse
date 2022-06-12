<?php

namespace App\Service\PullList\HandHeld\StowingProcess;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Events\PullList\StowingCompletedEvent;
use App\Repository\ItemSerialRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StowingProcessService
{
    public function __construct(
        private iterable $resolvers,
        private EntityManagerInterface $entityManager,
        private ItemSerialRepository $itemSerialRepository,
        private EventDispatcherInterface $dispatcher,
        private DocumentManager $documentManager
    ) {
    }

    public function stow(
        PullList $pullList,
        PullListItem $pullListItem,
        WarehouseStorageBin $storageBin,
        ItemSerial $itemSerial
    ): void {
        $this->entityManager->beginTransaction();

        try {
            /** @var StowingResolverInterface $resolver */
            foreach ($this->resolvers as $resolver) {
                $resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);
            }

            $this->dispatcher->dispatch(new StowingCompletedEvent($pullListItem));

            $this->entityManager->flush();
            $this->documentManager->flush();
            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->close();
            $this->entityManager->rollback();

            throw $exception;
        }
    }

    public function batchStow(PullListItem $pullListItem, WarehouseStorageBin $storageBin): void
    {
        $pullList = $pullListItem->getPullList();

        $itemSerials = $this->itemSerialRepository->getPullListItemSerials($pullListItem);

        foreach ($itemSerials as $itemSerial) {
            $this->stow($pullList, $pullListItem, $storageBin, $itemSerial);
        }
    }
}
