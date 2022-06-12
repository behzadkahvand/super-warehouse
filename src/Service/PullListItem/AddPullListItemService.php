<?php

namespace App\Service\PullListItem;

use App\Dictionary\PullListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\DTO\AddPullListItemData;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Service\PullListItem\Exceptions\AddPullListItemException;
use App\Service\PullListItem\Exceptions\InvalidReceiptItemStatusException;
use App\Service\PullListItem\Exceptions\InvalidReceiptItemWarehouseException;
use App\Service\PullListItem\Exceptions\PullListItemExistenceException;
use Doctrine\ORM\EntityManagerInterface;

class AddPullListItemService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected PullListItemFactory $factory
    ) {
    }

    public function perform(AddPullListItemData $data): void
    {
        $this->entityManager->beginTransaction();

        try {
            $pullList = $data->getPullList();

            $pullListWarehouseId = $pullList->getWarehouse()->getId();

            foreach ($data->getReceiptItems() as $receiptItem) {
                /**
                 * @var ReceiptItem $receiptItem
                 */
                if ($receiptItem->getStatus() !== ReceiptStatusDictionary::READY_TO_STOW) {
                    throw new InvalidReceiptItemStatusException();
                }

                $receipt = $receiptItem->getReceipt();

                if ($pullListWarehouseId !== $this->getReceiptItemWarehouseId($receipt)) {
                    throw new InvalidReceiptItemWarehouseException();
                }

                if ($receiptItem->getPullListItem()) {
                    throw new PullListItemExistenceException();
                }

                $pullListItem = $this->factory->getPullListItem();

                $quantity = $receiptItem->getQuantity();

                $pullListItem->setReceiptItem($receiptItem)
                             ->setReceipt($receipt)
                             ->setQuantity($quantity)
                             ->setRemainQuantity($quantity)
                             ->setPullList($pullList)
                             ->setStatus(PullListStatusDictionary::DRAFT);

                $pullList->addItem($pullListItem);

                $this->entityManager->persist($pullListItem);
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (AddPullListItemException $exception) {
            $this->entityManager->close();
            $this->entityManager->rollback();

            throw $exception;
        }
    }

    protected function getReceiptItemWarehouseId(Receipt $receipt): ?int
    {
        $receiptType = $receipt->getType();

        return match ($receiptType) {
            ReceiptTypeDictionary::GOOD_RECEIPT => $receipt->getSourceWarehouse()->getId(),
            ReceiptTypeDictionary::STOCK_TRANSFER => $receipt->getDestinationWarehouse()->getId(),
            default => null
        };
    }
}
