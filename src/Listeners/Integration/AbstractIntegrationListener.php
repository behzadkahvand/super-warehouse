<?php

namespace App\Listeners\Integration;

use App\Listeners\ChangeAware\AbstractChangeAwareListener;
use App\Service\Integration\IntegrationablePropertiesDiscoverService;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class AbstractIntegrationListener extends AbstractChangeAwareListener
{
    public function __construct(
        protected MessageBusInterface $eventBus,
        private IntegrationablePropertiesDiscoverService $discoverService
    ) {
    }

    protected function findChangeableProperties(object $entity): array
    {
        return $this->discoverService->getIntegrationableProperties(get_class($entity), $this->getFilterGroups());
    }

    protected function getIntegrationableProperties(): array
    {
        return $this->changeableProperties;
    }

    abstract protected function getFilterGroups(): array;
}
