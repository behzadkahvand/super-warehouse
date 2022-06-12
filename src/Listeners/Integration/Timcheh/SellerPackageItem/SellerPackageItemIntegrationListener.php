<?php

namespace App\Listeners\Integration\Timcheh\SellerPackageItem;

use App\Entity\SellerPackageItem;
use App\Listeners\ChangeAware\UpdateAwareTrait;
use App\Listeners\Integration\AbstractIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\SellerPackageItem\UpdateSellerPackageItemMessage;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SellerPackageItemIntegrationListener extends AbstractIntegrationListener
{
    use UpdateAwareTrait;

    protected function doPostUpdate(object $entity, array $changedProperties, LifecycleEventArgs $args): void
    {
        /**
         * @var SellerPackageItem $entity
         */
        $message = (new UpdateSellerPackageItemMessage())->setId($entity->getId())
                                                         ->setStatus($entity->getStatus())
                                                         ->setActualQuantity($entity->getActualQuantity());

        $this->eventBus->dispatch($message);
    }

    protected function getFilterGroups(): array
    {
        return ['timcheh.seller-package-item.update'];
    }
}
