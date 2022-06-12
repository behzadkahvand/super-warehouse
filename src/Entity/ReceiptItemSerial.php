<?php

namespace App\Entity;

use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\ReceiptItemSerialRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="receipt_item_serials")
 * @ORM\Entity(repositoryClass=ReceiptItemSerialRepository::class)
 */
class ReceiptItemSerial
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"receiptItem.read","receipt.list"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ReceiptItem::class, inversedBy="receiptItemSerials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiptItem;

    /**
     * @ORM\ManyToOne(targetEntity=ItemSerial::class, inversedBy="receiptItemSerials")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"receiptItem.read","receipt.list", "store.st-inbound"})
     */
    private $itemSerial;

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

    public function getItemSerial(): ?ItemSerial
    {
        return $this->itemSerial;
    }

    public function setItemSerial(?ItemSerial $itemSerial): self
    {
        $this->itemSerial = $itemSerial;

        return $this;
    }
}
