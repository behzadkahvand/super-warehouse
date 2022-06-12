<?php

namespace App\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\PickListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Repository\PickListRepository;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;
use App\Service\StatusTransition\StateTransitionHandlerService;

class CloseReceiptStatusResolver implements PickingResolverInterface
{
    public function __construct(
        private StateTransitionHandlerService $transitionHandlerService,
        private PickListRepository $pickListRepository
    ) {
    }

    public function resolve(PickList $pickList, ItemSerial $itemSerial): void
    {
        if (PickListStatusDictionary::CLOSE !== $pickList->getStatus()) {
            return;
        }

        $receiptPickList = $pickList->getReceiptItem()->getReceipt();

        $allReceiptPickLists = $this->pickListRepository->getAllReceiptPickList($receiptPickList);

        $pickListsNotClosed = collect($allReceiptPickLists)->filter(
            fn($pick) => (PickListStatusDictionary::CLOSE !== $pick->getStatus()) && ($pick->getId() !== $pickList->getId())
        );

        if ($pickListsNotClosed->isEmpty()) {
            $this->transitionHandlerService->batchTransitState(
                $receiptPickList->getReceiptItems()->toArray(),
                ReceiptStatusDictionary::DONE
            );
        }
    }

    public static function getPriority(): int
    {
        return 6;
    }
}
