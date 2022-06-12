<?php

namespace App\Listeners\Integration\Timcheh\SellerPackage;

use App\Entity\SellerPackage;
use App\Listeners\ChangeAware\UpdateAwareTrait;
use App\Listeners\Integration\AbstractIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\SellerPackage\UpdateSellerPackageMessage;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SellerPackageIntegrationListener extends AbstractIntegrationListener
{
    use UpdateAwareTrait;

    protected function doPostUpdate(object $entity, array $changedProperties, LifecycleEventArgs $args): void
    {
        /**
         * @var SellerPackage $entity
         */
        $message = (new UpdateSellerPackageMessage())->setId($entity->getId())
                                                     ->setStatus($entity->getStatus());

        $this->eventBus->dispatch($message);
    }

    protected function getFilterGroups(): array
    {
        return ['timcheh.seller-package.update'];
    }
}
