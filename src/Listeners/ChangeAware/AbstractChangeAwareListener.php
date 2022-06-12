<?php

namespace App\Listeners\ChangeAware;

abstract class AbstractChangeAwareListener
{
    protected array $changeableProperties;

    abstract protected function findChangeableProperties(object $entity): array;
}
