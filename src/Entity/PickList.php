<?php

namespace App\Entity;

use App\Dictionary\PickListStatusDictionary;
use App\DTO\AllowTransitionConfigData;
use App\DTO\StateSubscriberConfigData;
use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\PickListRepository;
use App\Service\StatusTransition\TransitionableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Table(name="pick_lists")
 * @ORM\Entity(repositoryClass=PickListRepository::class)
 */
class PickList implements TransitionableInterface
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"pick.list.index","pick.hand.held.list","pick.hand.held.picking", "pick.list.bug.report.read",})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"pick.list.index","pick.hand.held.list","pick.hand.held.picking", "pick.list.bug.report.read",})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"pick.list.index","pick.hand.held.list","pick.hand.held.picking", "pick.list.bug.report.read",})
     */
    private $priority;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"pick.list.index","pick.hand.held.list","pick.hand.held.picking", "pick.list.bug.report.read",})
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Admin::class)
     *
     * @Groups({"pick.list.index", "pick.list.bug.report.read",})
     */
    private $picker;

    /**
     * @ORM\ManyToOne(targetEntity=WarehouseStorageBin::class)
     *
     * @Groups({"pick.list.index","pick.hand.held.list", "pick.list.bug.report.read",})
     */
    private $storageBin;

    /**
     * @ORM\ManyToOne(targetEntity=WarehouseStorageArea::class)
     *
     * @Groups({"pick.list.index", "pick.list.bug.report.read",})
     */
    private $storageArea;

    /**
     * @ORM\ManyToOne(targetEntity=ReceiptItem::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"pick.list.index","pick.hand.held.list"})
     */
    private $receiptItem;

    /**
     * @ORM\ManyToOne(targetEntity=Warehouse::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"pick.list.index"})
     */
    private $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity=Shipment::class, fetch="EXTRA_LAZY")
     *
     * @Groups({"pick.list.index"})
     */
    private $shipment;

    /**
     * @ORM\OneToOne(targetEntity=PickListBugReport::class, mappedBy="pickList", cascade={"persist", "remove"})
     */
    private $pickListBugReport;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
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

    public function getPicker(): ?Admin
    {
        return $this->picker;
    }

    public function setPicker(?Admin $picker): self
    {
        $this->picker = $picker;

        return $this;
    }

    public function getStorageBin(): ?WarehouseStorageBin
    {
        return $this->storageBin;
    }

    public function setStorageBin(?WarehouseStorageBin $storageBin): self
    {
        $this->storageBin = $storageBin;

        return $this;
    }

    public function getStorageArea(): ?WarehouseStorageArea
    {
        return $this->storageArea;
    }

    public function setStorageArea(?WarehouseStorageArea $storageArea): self
    {
        $this->storageArea = $storageArea;

        return $this;
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

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function setShipment(?Shipment $shipment): self
    {
        $this->shipment = $shipment;

        return $this;
    }

    public function getShipment(): ?Shipment
    {
        return $this->shipment;
    }

    /**
     * @Groups({"pick.list.index","pick.hand.held.list","pick.hand.held.picking"})
     *
     * @SerializedName("remainedQuantity")
     */
    public function getRemainedQuantity(): int
    {
        return $this->getQuantity() - (int) $this->getReceiptItem()->getReceiptItemSerials()?->count();
    }

    public function getStatePropertyName(): string
    {
        return "status";
    }

    public function getAllowedTransitions(): AllowTransitionConfigData
    {
        return (new AllowTransitionConfigData())
            ->setDefault(PickListStatusDictionary::WAITING_FOR_ACCEPT)
            ->addAllowTransitions(PickListStatusDictionary::WAITING_FOR_ACCEPT, [PickListStatusDictionary::PICKING])
            ->addAllowTransitions(PickListStatusDictionary::PICKING, [PickListStatusDictionary::CLOSE]);
    }

    public function getStateSubscribers(): StateSubscriberConfigData
    {
        return (new StateSubscriberConfigData());
    }

    public function getPickListBugReport(): ?PickListBugReport
    {
        return $this->pickListBugReport;
    }

    public function setPickListBugReport(PickListBugReport $pickListBugReport): self
    {
        // set the owning side of the relation if necessary
        if ($pickListBugReport->getPickList() !== $this) {
            $pickListBugReport->setPickList($this);
        }

        $this->pickListBugReport = $pickListBugReport;

        return $this;
    }
}
