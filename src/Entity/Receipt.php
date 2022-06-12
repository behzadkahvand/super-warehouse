<?php

namespace App\Entity;

use App\DTO\AllowTransitionConfigData;
use App\DTO\StateSubscriberConfigData;
use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Entity\Receipt\GINoneReceipt;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\Receipt\GRNoneReceipt;
use App\Entity\Receipt\STInboundReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use App\Repository\ReceiptRepository;
use App\Service\StatusTransition\Traits\ReceiptFactoryTransitionTrait;
use App\Service\StatusTransition\TransitionableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use App\Annotations\Integrationable;

/**
 * @ORM\Table(name="receipts")
 * @ORM\Entity(repositoryClass=ReceiptRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="reference_type", type="string")
 * @ORM\DiscriminatorMap({
 *     "GI_NONE": GINoneReceipt::class,
 *     "GI_SHIPMENT": GIShipmentReceipt::class,
 *     "GR_MP_PACKAGE": GRMarketPlacePackageReceipt::class,
 *     "GR_NONE": GRNoneReceipt::class,
 *     "ST_INBOUND": STInboundReceipt::class,
 *     "ST_OUTBOUND": STOutboundReceipt::class,
 * })
 */
abstract class Receipt implements TransitionableInterface
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
     *     "receipt.list",
     *     "receipt.read",
     *     "receiptItem.read",
     *     "itemBatch.read",
     *     "itemBatchTransaction.read",
     *     "item.serial.transaction.read",
     *     "pick.list.index",
     *     "pull-list.receipt-item.add-list",
     *     "pullList.items.add",
     *     "pullList.items.index",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @Groups({
     *     "receipt.list",
     *     "receipt.read",
     *     "receiptItem.read",
     *     "item.serial.transaction.list",
     *     "item.serial.transaction.read",
     *     "pick.list.index",
     *     "pullList.items.add",
     * })
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"receipt.read"})
     *
     * @Assert\NotBlank(allowNull=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     *
     * @Groups({"receipt.read"})
     */
    private $costCenter;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"receipt.list", "receipt.read"})
     *
     * @Integrationable({"GIReceipt.status.update"})
     *
     * @Assert\NotBlank()
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Warehouse::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"receipt.read", "receipt.list"})
     *
     * @Assert\NotBlank()
     */
    private $sourceWarehouse;

    /**
     * @ORM\ManyToOne(targetEntity=Warehouse::class)
     *
     * @Groups({"receipt.read", "receipt.list"})
     */
    private $destinationWarehouse;

    /**
     * @ORM\OneToOne(targetEntity=Receipt::class, cascade={"persist", "remove"})
     *
     * @Groups({"receipt.read"})
     * @MaxDepth(1)
     */
    private $inboundReceipt;

    /**
     * @ORM\OneToMany(targetEntity=ReceiptItem::class, mappedBy="receipt")
     *
     * @Groups({"receipt.read","receipt.list"})
     */
    private $receiptItems;

    /**
     * @ORM\OneToMany(targetEntity=ItemBatch::class, mappedBy="receipt")
     *
     * @Groups({"receipt.read"})
     */
    private $itemBatches;

    public function __construct()
    {
        $this->receiptItems = new ArrayCollection();
        $this->itemBatches  = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    protected function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setCostCenter(?string $costCenter): self
    {
        $this->costCenter = $costCenter;

        return $this;
    }

    public function getCostCenter(): ?string
    {
        return $this->costCenter;
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

    public function getSourceWarehouse(): ?Warehouse
    {
        return $this->sourceWarehouse;
    }

    public function setSourceWarehouse(?Warehouse $sourceWarehouse): self
    {
        $this->sourceWarehouse = $sourceWarehouse;

        return $this;
    }

    public function getDestinationWarehouse(): ?Warehouse
    {
        return $this->destinationWarehouse;
    }

    public function setDestinationWarehouse(?Warehouse $destinationWarehouse): self
    {
        $this->destinationWarehouse = $destinationWarehouse;

        return $this;
    }

    public function getInboundReceipt(): ?self
    {
        return $this->inboundReceipt;
    }

    public function setInboundReceipt(?self $inboundReceipt): self
    {
        $this->inboundReceipt = $inboundReceipt;

        return $this;
    }

    /**
     * @return Collection|ReceiptItem[]
     */
    public function getReceiptItems(): Collection
    {
        return $this->receiptItems;
    }

    public function addReceiptItem(ReceiptItem $receiptItem): self
    {
        if (!$this->receiptItems->contains($receiptItem)) {
            $this->receiptItems[] = $receiptItem;
            $receiptItem->setReceipt($this);
        }

        return $this;
    }

    public function removeReceiptItem(ReceiptItem $receiptItem): self
    {
        if ($this->receiptItems->removeElement($receiptItem)) {
            // set the owning side to null (unless already changed)
            if ($receiptItem->getReceipt() === $this) {
                $receiptItem->setReceipt(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ItemBatch[]
     */
    public function getItemBatches(): Collection
    {
        return $this->itemBatches;
    }

    public function addItemBatch(ItemBatch $itemBatch): self
    {
        if (!$this->itemBatches->contains($itemBatch)) {
            $this->itemBatches[] = $itemBatch;
            $itemBatch->setReceipt($this);
        }

        return $this;
    }

    public function removeItemBatch(ItemBatch $itemBatch): self
    {
        if ($this->itemBatches->removeElement($itemBatch)) {
            // set the owning side to null (unless already changed)
            if ($itemBatch->getReceipt() === $this) {
                $itemBatch->setReceipt(null);
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
        return (new StateSubscriberConfigData());
    }

    public function getAllowedTransitions(): AllowTransitionConfigData
    {
        return $this->receiptFactoryTransition();
    }

    /**
     * @Groups({"receipt.list", "receipt.read",})
     */
    public function getReferenceId(): ?int
    {
        return $this->getReference()?->getId();
    }
}
