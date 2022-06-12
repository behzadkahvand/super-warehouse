<?php

namespace App\Listeners\ChangeAware;

trait InsertAwareTrait
{
    public function onPostPersist(object $entity): void
    {
        $this->changeableProperties = $this->findChangeableProperties($entity);

        $this->doPostPersist($entity);
    }

    abstract protected function doPostPersist(object $entity): void;
}
