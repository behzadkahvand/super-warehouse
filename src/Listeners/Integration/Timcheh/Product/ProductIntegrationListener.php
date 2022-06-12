<?php

namespace App\Listeners\Integration\Timcheh\Product;

use App\Listeners\ChangeAware\UpdateAwareTrait;
use App\Listeners\Integration\AbstractIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\Product\UpdateProductInTimchehMessage;
use Doctrine\ORM\Event\LifecycleEventArgs;

final class ProductIntegrationListener extends AbstractIntegrationListener
{
    use UpdateAwareTrait;

    protected function doPostUpdate(object $entity, array $changedProperties, LifecycleEventArgs $args): void
    {
        $message = new UpdateProductInTimchehMessage();
        $message->setId($entity->getId())
                ->setLength($entity->getLength())
                ->setWidth($entity->getWidth())
                ->setWeight($entity->getWeight())
                ->setHeight($entity->getHeight());

        $this->eventBus->dispatch($message);
    }

    protected function getFilterGroups(): array
    {
        return ['timcheh.product.update'];
    }
}
