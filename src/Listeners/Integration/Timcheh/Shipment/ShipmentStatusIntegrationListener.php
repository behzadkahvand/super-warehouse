<?php

namespace App\Listeners\Integration\Timcheh\Shipment;

use App\Dictionary\ShipmentStatusDictionary;
use App\Entity\Shipment;
use App\Listeners\ChangeAware\UpdateAwareTrait;
use App\Listeners\Integration\AbstractIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\UpdateShipmentStatusMessage;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ShipmentStatusIntegrationListener extends AbstractIntegrationListener
{
    use UpdateAwareTrait;

    /**
     * @var Shipment $entity
     */
    protected function doPostUpdate(object $entity, array $changedProperties, LifecycleEventArgs $args): void
    {
        if (ShipmentStatusDictionary::CANCELED === $entity->getStatus()) {
            return;
        }

        $message = (new UpdateShipmentStatusMessage())->setId($entity->getId())
                                                      ->setStatus($entity->getStatus());

        $this->eventBus->dispatch($message);
    }

    protected function getFilterGroups(): array
    {
        return ['timcheh.shipment.status.update'];
    }
}
