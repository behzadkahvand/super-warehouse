<?php

namespace App\Listeners\Integration\Timcheh\OrderItem;

use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Entity\ReceiptItem;
use App\Listeners\ChangeAware\UpdateAwareTrait;
use App\Listeners\Integration\AbstractIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\OrderItem\UpdateOrderItemInTimchehMessage;
use App\Repository\ShipmentItemRepository;
use App\Service\Integration\IntegrationablePropertiesDiscoverService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderItemIntegrationListener extends AbstractIntegrationListener
{
    use UpdateAwareTrait;

    public function __construct(
        MessageBusInterface $eventBus,
        IntegrationablePropertiesDiscoverService $discoverService,
        private ShipmentItemRepository $shipmentItemRepository
    ) {
        parent::__construct($eventBus, $discoverService);
    }

    protected function doPostUpdate(object $entity, array $changedProperties, LifecycleEventArgs $args): void
    {
        /** @var ReceiptItem $entity */

        $shipmentItem = $this->shipmentItemRepository->getPartialShipmentItemByReceiptItem($entity);

        if (!$shipmentItem || $entity->getReceipt()?->getReferenceType() !== ReceiptReferenceTypeDictionary::GI_SHIPMENT) {
            return;
        }

        $message = new UpdateOrderItemInTimchehMessage();
        $message->setId($shipmentItem->getId())
            ->setStatus($entity->getStatus());

        $this->eventBus->dispatch($message);
    }

    protected function getFilterGroups(): array
    {
        return ['timcheh.order.item.update'];
    }
}
