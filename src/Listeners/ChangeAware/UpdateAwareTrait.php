<?php

namespace App\Listeners\ChangeAware;

use Doctrine\ORM\Event\LifecycleEventArgs;

trait UpdateAwareTrait
{
    public function onPostUpdate(object $entity, LifecycleEventArgs $args): void
    {
        $this->changeableProperties = $this->findChangeableProperties($entity);
        if (!$this->changeableProperties) {
            return;
        }

        $changedProperties = $this->getChangedProperties($args, $entity);
        if (!$changedProperties) {
            return;
        }

        $this->doPostUpdate($entity, $changedProperties, $args);
    }

    private function getChangedProperties(LifecycleEventArgs $args, object $entity): array
    {
        $uow = $args->getEntityManager()->getUnitOfWork();
        $uow->computeChangeSets();
        $changeSet = $uow->getEntityChangeSet($entity);

        $changedProperties = [];
        if ($changeSet) {
            foreach ($changeSet as $property => $value) {
                if (in_array($property, $this->changeableProperties)) {
                    $changedProperties[] = $property;
                }
            }
        }

        return $changedProperties;
    }

    abstract protected function doPostUpdate(object $entity, array $changedProperties, LifecycleEventArgs $args): void;
}
