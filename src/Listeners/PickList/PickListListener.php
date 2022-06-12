<?php

namespace App\Listeners\PickList;

use App\Dictionary\ReceiptStatusDictionary;
use App\Events\PickList\GoodIssuePickListCreatedEvent;
use App\Events\PickList\PickListCreatedEventInterface;
use App\Events\PickList\ShipmentPickListCreatedEvent;
use App\Service\StatusTransition\StateTransitionHandlerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PickListListener implements EventSubscriberInterface
{
    public function __construct(private StateTransitionHandlerService $transitionHandlerService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ShipmentPickListCreatedEvent::class => 'updateReceiptAndReceiptItems',
            GoodIssuePickListCreatedEvent::class => 'updateReceiptAndReceiptItems',
        ];
    }

    public function updateReceiptAndReceiptItems(PickListCreatedEventInterface $event): void
    {
        $receipt = $event->getReceipt();
        $this->transitionHandlerService->batchTransitState($receipt->getReceiptItems()->toArray(), ReceiptStatusDictionary::READY_TO_PICK);
    }
}
