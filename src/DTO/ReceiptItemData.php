<?php

namespace App\DTO;

use App\Entity\Inventory;
use App\Entity\Receipt;
use Symfony\Component\Validator\Constraints as Assert;

final class ReceiptItemData extends BaseDTO
{
    /**
     * @Assert\NotBlank(groups={"receipt_item.create","receipt_item.update"})
     */
    private int $quantity;

    /**
     * @Assert\NotBlank(groups={"receipt_item.create"})
     */
    private ?Inventory $inventory = null;

    /**
     * @Assert\NotBlank(groups={"receipt_item.create"})
     */
    private ?Receipt $receipt = null;

    /**
     * @Assert\Choice(groups={"receipt_item.create"}, callback={"App\Dictionary\ReceiptTypeDictionary", "toArray"})
     * @Assert\NotBlank(groups={"receipt_item.create"})
     */
    private ?string $receiptType = null;

    /**
     * @param int $quantity
     *
     * @return ReceiptItemData
     */
    public function setQuantity(int $quantity): ReceiptItemData
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setReceiptType(?string $receiptType): ReceiptItemData
    {
        $this->receiptType = $receiptType;

        return $this;
    }

    public function getReceiptType(): ?string
    {
        return $this->receiptType;
    }

    public function setInventory(?Inventory $inventory): ReceiptItemData
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setReceipt(?Receipt $receipt): ReceiptItemData
    {
        $this->receipt = $receipt;

        return $this;
    }

    public function getReceipt(): ?Receipt
    {
        return $this->receipt;
    }
}
