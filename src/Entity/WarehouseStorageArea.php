<?php

namespace App\Entity;

use App\Entity\Common\Blameable;
use App\Entity\Common\Timestampable;
use App\Repository\WarehouseStorageAreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="warehouse_storage_areas")
 * @ORM\Entity(repositoryClass=WarehouseStorageAreaRepository::class)
 */
class WarehouseStorageArea
{
    use Timestampable;
    use Blameable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "warehouseStorageArea.list",
     *     "warehouseStorageArea.read",
     *     "warehouse.storage.bin.read",
     *     "warehouse.storage.bin.list",
     *     "pick.list.index",
     *     "pick.list.bug.report.read",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({
     *     "warehouseStorageArea.list",
     *     "warehouseStorageArea.read",
     *     "warehouse.storage.bin.read",
     *     "warehouse.storage.bin.list",
     *     "pick.list.bug.report.read",
     * })
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"warehouseStorageArea.list", "warehouseStorageArea.read"})
     */
    private $stowingStrategy;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"warehouseStorageArea.list", "warehouseStorageArea.read"})
     */
    private $capacityCheckMethod;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"warehouseStorageArea.list", "warehouseStorageArea.read"})
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Warehouse::class, inversedBy="warehouseStorageAreas")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"warehouseStorageArea.list", "warehouseStorageArea.read"})
     */
    private $warehouse;

    /**
     * @ORM\OneToMany(targetEntity=WarehouseStorageBin::class, mappedBy="warehouseStorageArea", orphanRemoval=true)
     */
    private $warehouseStorageBins;

    public function __construct()
    {
        $this->warehouseStorageBins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStowingStrategy(): ?string
    {
        return $this->stowingStrategy;
    }

    public function setStowingStrategy(string $stowingStrategy): self
    {
        $this->stowingStrategy = $stowingStrategy;

        return $this;
    }

    public function getCapacityCheckMethod(): ?string
    {
        return $this->capacityCheckMethod;
    }

    public function setCapacityCheckMethod(string $capacityCheckMethod): self
    {
        $this->capacityCheckMethod = $capacityCheckMethod;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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

    /**
     * @return Collection|WarehouseStorageBin[]
     */
    public function getWarehouseStorageBins(): Collection
    {
        return $this->warehouseStorageBins;
    }

    public function addWarehouseStorageBin(WarehouseStorageBin $warehouseStorageBin): self
    {
        if (!$this->warehouseStorageBins->contains($warehouseStorageBin)) {
            $this->warehouseStorageBins[] = $warehouseStorageBin;
            $warehouseStorageBin->setWarehouseStorageArea($this);
        }

        return $this;
    }

    public function removeWarehouseStorageBin(WarehouseStorageBin $warehouseStorageBin): self
    {
        if ($this->warehouseStorageBins->removeElement($warehouseStorageBin)) {
            // set the owning side to null (unless already changed)
            if ($warehouseStorageBin->getWarehouseStorageArea() === $this) {
                $warehouseStorageBin->setWarehouseStorageArea(null);
            }
        }

        return $this;
    }
}
