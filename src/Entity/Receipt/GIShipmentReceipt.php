<?php

namespace App\Entity\Receipt;

use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\Shipment;
use App\Repository\Receipt\GIShipmentReceiptRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass=GIShipmentReceiptRepository::class)
 */
class GIShipmentReceipt extends Receipt
{
    public function __construct()
    {
        parent::__construct();

        $this->setType(ReceiptTypeDictionary::GOOD_ISSUE);
    }

    /**
     * @ORM\ManyToOne(targetEntity=Shipment::class, inversedBy="receipt")
     * @ORM\JoinColumn(nullable=false, name="reference_id")
     * @MaxDepth(1)
     */
    private $reference;

    public function getReference(): ?Shipment
    {
        return $this->reference;
    }

    public function setReference(?Shipment $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @Groups({"receipt.list", "receipt.read",})
     */
    public function getReferenceType(): string
    {
        return ReceiptReferenceTypeDictionary::GI_SHIPMENT;
    }
}
