<?php

namespace App\Listeners\Integration\Timcheh\Warehouse;

use App\Entity\Warehouse;
use App\Listeners\ChangeAware\InsertAwareTrait;
use App\Listeners\ChangeAware\UpdateAwareTrait;
use App\Listeners\Integration\AbstractIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\Warehouse\AbstractWarehouseMessage;
use App\Messaging\Messages\Event\Integration\Timcheh\Warehouse\CreateWarehouseMessage;
use App\Messaging\Messages\Event\Integration\Timcheh\Warehouse\UpdateWarehouseMessage;
use Doctrine\ORM\Event\LifecycleEventArgs;

final class WarehouseIntegrationListener extends AbstractIntegrationListener
{
    use InsertAwareTrait;
    use UpdateAwareTrait;

    protected function doPostPersist(object $entity): void
    {
        $message = new CreateWarehouseMessage();

        $this->setMessageFieldsAndDispatch($message, $entity);
    }

    protected function doPostUpdate(object $entity, array $changedProperties, LifecycleEventArgs $args): void
    {
        $message = new UpdateWarehouseMessage();

        $this->setMessageFieldsAndDispatch($message, $entity);
    }

    private function setMessageFieldsAndDispatch(AbstractWarehouseMessage $message, Warehouse $warehouse): void
    {
        $message->setId($warehouse->getId())
                ->setTitle($warehouse->getTitle())
                ->setIsActive($warehouse->getIsActive())
                ->setAddress($warehouse->getAddress())
                ->setCoordinates($warehouse->getCoordinates())
                ->setForFmcgMarketPlacePurchase($warehouse->getForFmcgMarketPlacePurchase())
                ->setForMarketPlacePurchase($warehouse->getForMarketPlacePurchase())
                ->setForRetailPurchase($warehouse->getForRetailPurchase())
                ->setForSale($warehouse->getForSale())
                ->setForSalesReturn($warehouse->getForSalesReturn())
                ->setPhone($warehouse->getPhone());

        $this->eventBus->dispatch($message);
    }

    protected function getFilterGroups(): array
    {
        return ['timcheh.warehouse.update'];
    }
}
