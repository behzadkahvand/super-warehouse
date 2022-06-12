<?php

namespace App\Messaging\Handlers\Command\ItemSerial;

use App\Document\ItemSerialTransaction;
use App\Messaging\Messages\Command\ItemSerial\ItemSerialTransactionMessage;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ItemSerialTransactionHandler implements MessageHandlerInterface
{
    public function __construct(protected DocumentManager $documentManager)
    {
    }

    public function __invoke(ItemSerialTransactionMessage $itemSerialTransactionMessage): void
    {
        $itemSerialTransaction = (new ItemSerialTransaction())
            ->setItemSerialId($itemSerialTransactionMessage->getItemSerialId())
            ->setReceiptId($itemSerialTransactionMessage->getReceiptId())
            ->setActionType($itemSerialTransactionMessage->getActionType())
            ->setWarehouseId($itemSerialTransactionMessage->getWarehouseId())
            ->setWarehouseStorageBinId($itemSerialTransactionMessage->getWarehouseStorageBinId())
            ->setUpdatedBy($itemSerialTransactionMessage->getUpdatedBy())
            ->setUpdatedAt($itemSerialTransactionMessage->getUpdatedAt());

        $this->documentManager->persist($itemSerialTransaction);
        $this->documentManager->flush();
    }
}
