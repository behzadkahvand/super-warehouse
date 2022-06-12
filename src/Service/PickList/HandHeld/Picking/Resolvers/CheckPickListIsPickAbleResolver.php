<?php

namespace App\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\PickListStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\PickList\HandHeld\Exceptions\PickListNotPickableException;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;
use Symfony\Component\Security\Core\Security;

class CheckPickListIsPickAbleResolver implements PickingResolverInterface
{
    public function __construct(private Security $security)
    {
    }

    public function resolve(PickList $pickList, ItemSerial $itemSerial): void
    {
        if (
            ($pickList->getPicker()->getId() !== $this->security->getUser()->getId()) ||
            (PickListStatusDictionary::PICKING !== $pickList->getStatus())
        ) {
            throw new PickListNotPickableException();
        }
    }

    public static function getPriority(): int
    {
        return 20;
    }
}
