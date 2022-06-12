<?php

namespace App\Service\StatusTransition\Traits;

use App\DTO\StateSubscriberConfigData;
use App\Service\StatusTransition\Subscribers\Receipt\ApproveManualReceiptItemsStateSubscriber;

trait NoneReferenceReceiptSubscriberTrait
{
    public function getStateSubscribers(): StateSubscriberConfigData
    {
        return (new StateSubscriberConfigData())
            ->addSubscriber(ApproveManualReceiptItemsStateSubscriber::class);
    }
}
