<?php

namespace App\Service\PickList\HandHeld\Picking;

use App\Entity\ItemSerial;
use App\Entity\PickList;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Events\PickList\PickingCompletedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HandHeldPickingService
{
    public function __construct(
        private iterable $resolvers,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $dispatcher,
        private DocumentManager $documentManager
    ) {
    }

    public function pick(PickList $pickList, ItemSerial $itemSerial): void
    {
        $this->entityManager->beginTransaction();

        try {
            /** @var PickingResolverInterface $resolver */
            foreach ($this->resolvers as $resolver) {
                $resolver->resolve($pickList, $itemSerial);
            }

            $this->dispatcher->dispatch(new PickingCompletedEvent($pickList));

            $this->entityManager->flush();
            $this->documentManager->flush();
            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->close();
            $this->entityManager->rollback();

            throw $exception;
        }
    }
}
