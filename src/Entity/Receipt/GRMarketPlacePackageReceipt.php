<?php

namespace App\Entity\Receipt;

use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\SellerPackage;
use App\Repository\Receipt\GRMarketPlacePackageReceiptRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GRMarketPlacePackageReceiptRepository::class)
 */
class GRMarketPlacePackageReceipt extends Receipt
{
    public function __construct()
    {
        parent::__construct();

        $this->setType(ReceiptTypeDictionary::GOOD_RECEIPT);
    }

    /**
     * @ORM\ManyToOne(targetEntity=SellerPackage::class)
     * @ORM\JoinColumn(nullable=false, name="reference_id")
     */
    private $reference;

    public function getReference(): ?SellerPackage
    {
        return $this->reference;
    }

    public function setReference(?SellerPackage $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @Groups({"receipt.list", "receipt.read",})
     */
    public function getReferenceType(): string
    {
        return ReceiptReferenceTypeDictionary::GR_MP_PACKAGE;
    }
}
