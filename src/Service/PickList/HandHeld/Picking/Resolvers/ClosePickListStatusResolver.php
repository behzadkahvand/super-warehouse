<?php

namespace App\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\PickListStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;
use App\Service\StatusTransition\StateTransitionHandlerService;

class ClosePickListStatusResolver implements PickingResolverInterface
{
    public function __construct(private StateTransitionHandlerService $transitionHandlerService)
    {
    }

    public function resolve(PickList $pickList, ItemSerial $itemSerial): void
    {
        $remainQuantity = $pickList->getRemainedQuantity();

        if ($remainQuantity == 0) {
            $this->transitionHandlerService->transitState($pickList, PickListStatusDictionary::CLOSE);
        }
    }

    public static function getPriority(): int
    {
        return 8;
    }
}
