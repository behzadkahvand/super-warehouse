<?php

namespace App\Service\StatusTransition\Subscribers\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\StateSubscriberData;
use App\Entity\Receipt;
use App\Service\StatusTransition\StateTransitionHandlerService;
use App\Service\StatusTransition\Subscribers\StateSubscriberInterface;

class ApproveManualReceiptItemsStateSubscriber implements StateSubscriberInterface
{
    public function __construct(private StateTransitionHandlerService $transitionHandlerService)
    {
    }

    public function __invoke(StateSubscriberData $stateSubscriberData): void
    {
        /** @var Receipt $receipt */
        $receipt = $stateSubscriberData->getEntityObject();

        if ($receipt->getStatus() !== ReceiptStatusDictionary::APPROVED) {
            return;
        }

        $receiptItems = $receipt->getReceiptItems();

        if ($receiptItems->isEmpty()) {
            return;
        }

        $this->transitionHandlerService->batchTransitState($receiptItems->toArray(), ReceiptStatusDictionary::APPROVED);
    }
}
