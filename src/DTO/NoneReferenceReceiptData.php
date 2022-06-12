<?php

namespace App\DTO;

use App\Entity\Warehouse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class NoneReferenceReceiptData
{
    /**
     * @Assert\Choice(
     *     groups={"receipt.manual.store", "receipt.manual.update",},
     *     callback={"App\Dictionary\ReceiptTypeDictionary", "toArray"}
     * )
     * @Assert\NotBlank(groups={"receipt.manual.store","receipt.manual.update"})
     * @Groups({"receipt.manual.store","receipt.manual.update"})
     */
    private string $type;

    /**
     * @Assert\NotBlank(groups={"receipt.manual.store", "receipt.manual.update",})
     * @Groups({"receipt.manual.store", "receipt.manual.update"})
     */
    private ?Warehouse $sourceWarehouse = null;

    /**
     * @Assert\NotBlank(groups={"receipt.stock_transfer.create","receipt.stock_transfer.update"})
     * @Assert\Blank(groups={"receipt.good_receipt.create","receipt.good_receipt.update","receipt.good_issue.create","receipt.good_issue.update"})
     * @Groups({"receipt.manual.store", "receipt.manual.update"})
     */
    private ?Warehouse $destinationWarehouse = null;

    /**
     * @Assert\Choice(
     *     groups={"receipt.good_receipt.create","receipt.good_receipt.update","receipt.good_issue.create","receipt.good_issue.update"},
     *     callback={"App\Dictionary\CostCenterDictionary", "toArray"}
     * )
     * @Assert\Blank(groups={"receipt.stock_transfer.create","receipt.stock_transfer.update"})
     * @Assert\NotBlank(groups={"receipt.good_issue.create","receipt.good_issue.update"})
     * @Groups({"receipt.manual.store", "receipt.manual.update"})
     */
    private ?string $costCenter = null;

    /**
     * @Groups({"receipt.manual.store", "receipt.manual.update"})
     */
    private ?string $description = null;

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setSourceWarehouse(?Warehouse $sourceWarehouse): self
    {
        $this->sourceWarehouse = $sourceWarehouse;

        return $this;
    }

    public function getSourceWarehouse(): ?Warehouse
    {
        return $this->sourceWarehouse;
    }

    public function setDestinationWarehouse(?Warehouse $destinationWarehouse): self
    {
        $this->destinationWarehouse = $destinationWarehouse;

        return $this;
    }

    public function getDestinationWarehouse(): ?Warehouse
    {
        return $this->destinationWarehouse;
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
