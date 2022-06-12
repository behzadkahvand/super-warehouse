<?php

namespace App\Service\PickList;

use App\Dictionary\PickListPriorityDictionary;
use App\Dictionary\PickListStatusDictionary;
use App\Entity\Receipt;
use App\Events\PickList\GoodIssuePickListCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class GoodIssuePickListService
{
    public function __construct(
        private PickListFactory $pickListFactory,
        private EntityManagerInterface $manager,
        private EventDispatcherInterface $dispatcher
    ) {
    }

    public function create(Receipt $receipt): void
    {
        foreach ($receipt->getReceiptItems() as $receiptItem) {
            $pickList = $this->pickListFactory->create();
            $pickList->setWarehouse($receipt->getSourceWarehouse())
                     ->setPriority(PickListPriorityDictionary::MEDIUM)
                     ->setStatus(PickListStatusDictionary::WAITING_FOR_ACCEPT)
                     ->setQuantity($receiptItem->getQuantity())
                     ->setReceiptItem($receiptItem);
            $this->manager->persist($pickList);
        }
        $this->manager->flush();

        $this->dispatcher->dispatch(new GoodIssuePickListCreatedEvent($receipt));
    }
}
