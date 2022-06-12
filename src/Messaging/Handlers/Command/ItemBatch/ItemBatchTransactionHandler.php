<?php

namespace App\Messaging\Handlers\Command\ItemBatch;

use App\Document\ItemBatchTransaction;
use App\Messaging\Messages\Command\ItemBatch\ItemBatchTransactionMessage;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ItemBatchTransactionHandler implements MessageHandlerInterface
{
    public function __construct(protected DocumentManager $documentManager)
    {
    }

    public function __invoke(ItemBatchTransactionMessage $itemBatchTransactionMessage): void
    {
        $itemBatchTransaction = (new ItemBatchTransaction())
            ->setItemBatchId($itemBatchTransactionMessage->getItemBatchId())
            ->setReceiptId($itemBatchTransactionMessage->getReceiptId())
            ->setQuantity($itemBatchTransactionMessage->getQuantity())
            ->setActionType($itemBatchTransactionMessage->getActionType())
            ->setWarehouseId($itemBatchTransactionMessage->getWarehouseId())
            ->setWarehouseStorageBinId($itemBatchTransactionMessage->getWarehouseStorageBinId())
            ->setUpdatedBy($itemBatchTransactionMessage->getUpdatedBy())
            ->setUpdatedAt($itemBatchTransactionMessage->getUpdatedAt())
            ->setCreatedAt($itemBatchTransactionMessage->getUpdatedAt());

        $this->documentManager->persist($itemBatchTransaction);
        $this->documentManager->flush();
    }
}
