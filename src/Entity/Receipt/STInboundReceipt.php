<?php

namespace App\Entity\Receipt;

use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Repository\Receipt\STInboundReceiptRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass=STInboundReceiptRepository::class)
 */
class STInboundReceipt extends Receipt
{
    public function __construct()
    {
        parent::__construct();

        $this->setType(ReceiptTypeDictionary::STOCK_TRANSFER);
    }

    /**
     * @ORM\ManyToOne(targetEntity=Receipt::class)
     * @ORM\JoinColumn(nullable=false, name="reference_id")
     * @MaxDepth(1)
     */
    private $reference;

    public function getReference(): ?Receipt
    {
        return $this->reference;
    }

    public function setReference(?Receipt $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @Groups({"receipt.list", "receipt.read",})
     */
    public function getReferenceType(): string
    {
        return ReceiptReferenceTypeDictionary::ST_INBOUND;
    }
}
