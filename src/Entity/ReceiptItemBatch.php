<?php

namespace App\Entity;

use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\ReceiptItemBatchRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="receipt_item_batches")
 * @ORM\Entity(repositoryClass=ReceiptItemBatchRepository::class)
 */
class ReceiptItemBatch
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"receiptItem.read", "itemBatch.read", "receiptItemBatch.list", "receiptItemBatch.read","receipt.list"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ReceiptItem::class, inversedBy="receiptItemBatches")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"receiptItemBatch.list", "receiptItemBatch.read"})
     */
    private $receiptItem;

    /**
     * @ORM\ManyToOne(targetEntity=ItemBatch::class, inversedBy="receiptItemBatches")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"receiptItemBatch.list", "receiptItemBatch.read","receipt.list", "store.st-inbound"})
     */
    private $itemBatch;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReceiptItem(): ?ReceiptItem
    {
        return $this->receiptItem;
    }

    public function setReceiptItem(?ReceiptItem $receiptItem): self
    {
        $this->receiptItem = $receiptItem;

        return $this;
    }

    public function getItemBatch(): ?ItemBatch
    {
        return $this->itemBatch;
    }

    public function setItemBatch(?ItemBatch $itemBatch): self
    {
        $this->itemBatch = $itemBatch;

        return $this;
    }
}
