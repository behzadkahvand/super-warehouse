<?php

namespace App\Service\StatusTransition\Subscribers\Receipt;

use App\Dictionary\ReceiptSortedStatusDictionary;
use App\DTO\StateSubscriberData;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Service\StatusTransition\ParentItemStateService;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Service\StatusTransition\Subscribers\StateSubscriberInterface;

class ReceiptStateDecisionMakerSubscriber implements StateSubscriberInterface
{
    public function __construct(
        private StateTransitionHandlerService $transitionHandlerService,
        private ParentItemStateService $parentItemStateService
    ) {
    }

    public function __invoke(StateSubscriberData $stateSubscriberData): void
    {
        /** @var ReceiptItem $receiptItem */
        $receiptItem = $stateSubscriberData->getEntityObject();

        /** @var Receipt $receipt */
        $receipt   = $receiptItem->getReceipt();
        $className = get_class_name_from_object($receipt);

        $itemStatuses = $this->getReceiptItemsStatus($receipt);

        $parentNextStatus = $this->parentItemStateService->findLowestStatusItems(
            ReceiptSortedStatusDictionary::class,
            strtoupper($className),
            $itemStatuses
        );

        if ($parentNextStatus === $receipt->getStatus()) {
            return;
        }

        $this->transitionHandlerService->transitState($receipt, $parentNextStatus);
    }

    protected function getReceiptItemsStatus(Receipt $receipt): array
    {
        $itemStatuses = [];
        foreach ($receipt->getReceiptItems() as $item) {
            $itemStatuses[] = $item->getStatus();
        }

        return $itemStatuses;
    }
}
