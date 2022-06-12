<?php

namespace App\Entity;

use App\Annotations\Integrationable;
use App\DTO\AllowTransitionConfigData;
use App\DTO\StateSubscriberConfigData;
use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\ReceiptItemRepository;
use App\Service\StatusTransition\Subscribers\Receipt\ReceiptStateDecisionMakerSubscriber;
use App\Service\StatusTransition\Traits\ReceiptFactoryTransitionTrait;
use App\Service\StatusTransition\TransitionableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="receipt_items")
 * @ORM\Entity(repositoryClass=ReceiptItemRepository::class)
 */
class ReceiptItem implements TransitionableInterface
{
    use Timestampable;
    use Blameable;
    use ReceiptFactoryTransitionTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "receiptItem.list",
     *     "receiptItem.read",
     *     "receipt.read",
     *     "receiptItemBatch.list",
     *     "receiptItemBatch.read",
     *     "pick.list.index",
     *     "pull-list.receipt-item.add-list",
     *     "pullList.items.add",
     *     "pick.hand.held.list",
     *     "receipt.list",
     *     "pullList.items.index",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "receiptItem.list",
     *     "receiptItem.read",
     *     "receipt.read",
     *     "pull-list.receipt-item.add-list",
     *     "pullList.items.add",
     *     "receipt.list",
     * })
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"receiptItem.list", "receiptItem.read", "receipt.read", "receipt.list"})
     *
     * @Integrationable({"timcheh.order.item.update"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Receipt::class, inversedBy="receiptItems")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({
     *     "receiptItem.read",
     *     "pull-list.receipt-item.add-list",
     *     "pick.list.index",
     * })
     */
    private $receipt;

    /**
     * @ORM\ManyToOne(targetEntity=Inventory::class, inversedBy="receiptItems")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({
     *     "receipt.read",
     *     "receiptItem.read",
     *     "pick.list.index",
     *     "pick.hand.held.list",
     *     "receipt.list",
     *     "pull-list.receipt-item.add-list",
     *     "pull-list.hand-held.active-for-locate",
     *     "pull-list.hand-held.show-active-list",
     * })
     */
    private $inventory;

    /**
     * @ORM\OneToMany(targetEntity=ReceiptItemBatch::class, mappedBy="receiptItem", cascade={"persist", "remove"})
     *
     * @Groups({"receiptItem.read","receipt.list", "store.st-inbound",})
     */
    private $receiptItemBatches;

    /**
     * @ORM\OneToMany(targetEntity=ReceiptItemSerial::class, mappedBy="receiptItem")
     *
     * @Groups({"receiptItem.read","receipt.list", "store.st-inbound",})
     */
    private $receiptItemSerials;

    /**
     * @ORM\OneToOne(targetEntity=PullListItem::class, mappedBy="receiptItem")
     */
    private $pullListItem;

    public function __construct()
    {
        $this->receiptItemBatches = new ArrayCollection();
        $this->receiptItemSerials = new ArrayCollection();
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    public function setInventory(?Inventory $inventory): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getPullListItem(): ?PullListItem
    {
        return $this->pullListItem;
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
            $receiptItemBatch->setReceiptItem($this);
        }

        return $this;
    }

    public function removeReceiptItemBatch(ReceiptItemBatch $receiptItemBatch): self
    {
        if ($this->receiptItemBatches->removeElement($receiptItemBatch)) {
            // set the owning side to null (unless already changed)
            if ($receiptItemBatch->getReceiptItem() === $this) {
                $receiptItemBatch->setReceiptItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReceiptItemSerial[]
     */
    public function getReceiptItemSerials(): ?Collection
    {
        return $this->receiptItemSerials;
    }

    public function addReceiptItemSerial(ReceiptItemSerial $receiptItemSerial): self
    {
        if (!$this->receiptItemSerials->contains($receiptItemSerial)) {
            $this->receiptItemSerials[] = $receiptItemSerial;
            $receiptItemSerial->setReceiptItem($this);
        }

        return $this;
    }

    public function removeReceiptItemSerial(ReceiptItemSerial $receiptItemSerial): self
    {
        if ($this->receiptItemSerials->removeElement($receiptItemSerial)) {
            // set the owning side to null (unless already changed)
            if ($receiptItemSerial->getReceiptItem() === $this) {
                $receiptItemSerial->setReceiptItem(null);
            }
        }

        return $this;
    }

    public function getStatePropertyName(): string
    {
        return "status";
    }

    public function getStateSubscribers(): StateSubscriberConfigData
    {
        return (new StateSubscriberConfigData())
            ->addSubscriber(ReceiptStateDecisionMakerSubscriber::class, 1);
    }

    public function getAllowedTransitions(): AllowTransitionConfigData
    {
        return $this->receiptFactoryTransition($this->getReceipt());
    }

    public function getRemainedQuantity(): int
    {
        return (int)$this->getQuantity() - (int)$this->getReceiptItemSerials()?->count();
    }
}
