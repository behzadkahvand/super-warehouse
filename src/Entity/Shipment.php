<?php

namespace App\Entity;

use App\Repository\ShipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Annotations\Integrationable;

/**
 * @ORM\Table(name="shipments")
 * @ORM\Entity(repositoryClass=ShipmentRepository::class)
 */
class Shipment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Integrationable({"timcheh.shipment.status.update"})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $category;

    /**
     * @ORM\Column(type="date")
     */
    private $deliveryDate;

    /**
     * @ORM\OneToMany(targetEntity=ShipmentItem::class, mappedBy="shipment")
     */
    private $shipmentItems;

    /**
     * @ORM\OneToMany(targetEntity=GIShipmentReceipt::class, mappedBy="reference")
     */
    private $receipt;

    public function __construct()
    {
        $this->shipmentItems = new ArrayCollection();
        $this->receipt       = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * @return Collection|ShipmentItem[]
     */
    public function getShipmentItems(): Collection
    {
        return $this->shipmentItems;
    }

    public function addShipmentItem(ShipmentItem $shipmentItem): self
    {
        if (!$this->shipmentItems->contains($shipmentItem)) {
            $this->shipmentItems[] = $shipmentItem;
            $shipmentItem->setShipment($this);
        }

        return $this;
    }

    public function removeShipmentItem(ShipmentItem $shipmentItem): self
    {
        if ($this->shipmentItems->removeElement($shipmentItem)) {
            // set the owning side to null (unless already changed)
            if ($shipmentItem->getShipment() === $this) {
                $shipmentItem->setShipment(null);
            }
        }

        return $this;
    }

    public function getReceipt(): Receipt
    {
        $criteria = Criteria::create();

        $criteria
            ->orderBy([
                'id' => Criteria::DESC,
            ])
            ->setFirstResult(0)
            ->setMaxResults(1);

        return $this->receipt->matching($criteria)->first();
    }

    public function addReceipt(Receipt $receipt): self
    {
        if (!$this->receipt->contains($receipt)) {
            $this->receipt[] = $receipt;
            $receipt->setReference($this);
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): self
    {
        if ($this->receipt->removeElement($receipt)) {
            // set the owning side to null (unless already changed)
            if ($receipt->getReference() === $this) {
                $receipt->setReference(null);
            }
        }

        return $this;
    }
}
