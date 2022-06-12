<?php

namespace App\Entity;

use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\ItemBatchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="item_batches")
 * @ORM\Entity(repositoryClass=ItemBatchRepository::class)
 */
class ItemBatch
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "receipt.read",
     *     "itemBatch.list",
     *     "itemBatch.read",
     *     "receiptItemBatch.list",
     *     "receiptItemBatch.read",
     *     "item.serial.read",
     *     "pick.list.index",
     *     "pick.hand.held.list",
     *     "receipt.list"
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"receipt.read", "itemBatch.list", "itemBatch.read","receipt.list"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="date", nullable=true)
     *
     * @Groups({"itemBatch.list", "itemBatch.read","receipt.list"})
     */
    private $expireAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"itemBatch.list", "itemBatch.read","receipt.list"})
     */
    private $supplierBarcode;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     *
     * @Groups({"itemBatch.list", "itemBatch.read","receipt.list"})
     */
    private $consumerPrice;

    /**
     * @ORM\ManyToOne(targetEntity=Inventory::class, inversedBy="itemBatches")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"itemBatch.read"})
     */
    private $inventory;

    /**
     * @ORM\OneToMany(targetEntity=ItemSerial::class, mappedBy="itemBatch")
     *
     * @Groups({"pick.hand.held.list",})
     */
    private $itemSerials;

    /**
     * @ORM\OneToMany(targetEntity=ReceiptItemBatch::class, mappedBy="itemBatch", cascade={"persist", "remove"})
     *
     * @Groups({"itemBatch.read"})
     */
    private $receiptItemBatches;

    /**
     * @ORM\ManyToOne(targetEntity=Receipt::class, inversedBy="itemBatches")
     * @ORM\JoinColumn(nullable=false, name="receipt_id", referencedColumnName="id")
     *
     * @Groups({"itemBatch.read"})
     */
    private $receipt;

    public function __construct()
    {
        $this->itemSerials           = new ArrayCollection();
        $this->receiptItemBatches    = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeInterface
    {
        return $this->expireAt;
    }

    public function setExpireAt(?\DateTimeInterface $expireAt): self
    {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getSupplierBarcode(): ?string
    {
        return $this->supplierBarcode;
    }

    public function setSupplierBarcode(?string $supplierBarcode): self
    {
        $this->supplierBarcode = $supplierBarcode;

        return $this;
    }

    public function getConsumerPrice(): ?int
    {
        return $this->consumerPrice;
    }

    public function setConsumerPrice(?int $consumerPrice): self
    {
        $this->consumerPrice = $consumerPrice;

        return $this;
    }

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setInventory(?Inventory $inventory): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * @return Collection|ItemSerial[]
     */
    public function getItemSerials(): Collection
    {
        return $this->itemSerials;
    }

    public function addItemSerial(ItemSerial $itemSerial): self
    {
        if (!$this->itemSerials->contains($itemSerial)) {
            $this->itemSerials[] = $itemSerial;
            $itemSerial->setItemBatch($this);
        }

        return $this;
    }

    public function removeItemSerial(ItemSerial $itemSerial): self
    {
        if ($this->itemSerials->removeElement($itemSerial)) {
            // set the owning side to null (unless already changed)
            if ($itemSerial->getItemBatch() === $this) {
                $itemSerial->setItemBatch(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReceiptItemBatch[]
     */
    public function getReceiptItemBatches(): Collection
    {
        return $this->receiptItemBatches;
    }

    public function addReceiptItemBatch(ReceiptItemBatch $receiptItemBatch): self
    {
        if (!$this->receiptItemBatches->contains($receiptItemBatch)) {
            $this->receiptItemBatches[] = $receiptItemBatch;
            $receiptItemBatch->setItemBatch($this);
        }

        return $this;
    }

    public function removeReceiptItemBatch(ReceiptItemBatch $receiptItemBatch): self
    {
        if ($this->receiptItemBatches->removeElement($receiptItemBatch)) {
            // set the owning side to null (unless already changed)
            if ($receiptItemBatch->getItemBatch() === $this) {
                $receiptItemBatch->setItemBatch(null);
            }
        }

        return $this;
    }

    public function getReceipt(): ?Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(?Receipt $receipt): self
    {
        $this->receipt = $receipt;

        return $this;
    }
}
