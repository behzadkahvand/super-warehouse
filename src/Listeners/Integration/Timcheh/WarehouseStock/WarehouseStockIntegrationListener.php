<?php

namespace App\Listeners\Integration\Timcheh\WarehouseStock;

use App\Entity\WarehouseStock;
use App\Listeners\ChangeAware\UpdateAwareTrait;
use App\Listeners\Integration\AbstractIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\WarehouseStock\UpdateInventorySaleableStockInTimchehMessage;
use Doctrine\ORM\Event\LifecycleEventArgs;

final class WarehouseStockIntegrationListener extends AbstractIntegrationListener
{
    use UpdateAwareTrait;

    protected function doPostUpdate(object $entity, array $changedProperties, LifecycleEventArgs $args): void
    {
        /** @var WarehouseStock $entity */

        $message = new UpdateInventorySaleableStockInTimchehMessage();
        $message->setId($entity->getInventory()->getId())
            ->setSaleableStock($entity->getSaleableStock());

        $this->eventBus->dispatch($message);
    }

    protected function getFilterGroups(): array
    {
        return ['timcheh.warehouse.stock.update'];
    }
}
