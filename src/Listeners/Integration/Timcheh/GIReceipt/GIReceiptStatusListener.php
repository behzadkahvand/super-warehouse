<?php

namespace App\Listeners\Integration\Timcheh\GIReceipt;

use App\Dictionary\ShipmentGIReceiptMappingStatusDictionary;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Listeners\ChangeAware\UpdateAwareTrait;
use App\Listeners\Integration\AbstractIntegrationListener;
use App\Service\Integration\IntegrationablePropertiesDiscoverService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

class GIReceiptStatusListener extends AbstractIntegrationListener
{
    use UpdateAwareTrait;

    public function __construct(
        MessageBusInterface $eventBus,
        IntegrationablePropertiesDiscoverService $discoverService,
        private EntityManagerInterface $manager
    ) {
        parent::__construct($eventBus, $discoverService);
    }

    /**
     * @var GIShipmentReceipt $entity
     */
    protected function doPostUpdate(object $entity, array $changedProperties, LifecycleEventArgs $args): void
    {
        $mappedStatus = ShipmentGIReceiptMappingStatusDictionary::toArray();

        $entity->getReference()
               ->setStatus($mappedStatus['MAPPED_STATUS'][$entity->getStatus()]);

        $this->manager->flush();
    }

    protected function getFilterGroups(): array
    {
        return ['GIReceipt.status.update'];
    }
}
