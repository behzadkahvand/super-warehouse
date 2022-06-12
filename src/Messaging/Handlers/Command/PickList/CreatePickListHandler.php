<?php

namespace App\Messaging\Handlers\Command\PickList;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Shipment;
use App\Events\PickList\ShipmentPickListCreatedEvent;
use App\Messaging\Messages\Command\PickList\CreatePickListMessage;
use App\Repository\ShipmentRepository;
use App\Service\PickList\PickListService;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Throwable;

final class CreatePickListHandler implements MessageHandlerInterface
{
    public function __construct(
        private ShipmentRepository $repository,
        private PickListService $pickListService,
        private EventDispatcherInterface $dispatcher,
        private EntityManagerInterface $manager
    ) {
    }

    public function __invoke(CreatePickListMessage $createPickListMessage): void
    {
        $this->manager->beginTransaction();
        try {
            /** @var Shipment $shipment */
            $shipment = $this->repository->find($createPickListMessage->getShipmentId(), LockMode::PESSIMISTIC_READ);

            if ($shipment->getReceipt()->getStatus() === ReceiptStatusDictionary::READY_TO_PICK) {
                throw new \Exception();
            }

            foreach ($shipment->getShipmentItems() as $shipmentItem) {
                $this->pickListService->create($shipmentItem->getReceiptItem(), true);
            }

            $this->dispatcher->dispatch(new ShipmentPickListCreatedEvent($shipment));

            $this->manager->commit();
        } catch (Throwable $exception) {
            $this->manager->close();
            $this->manager->rollback();
        }
    }
}
