<?php

namespace App\Entity\Receipt;

use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Repository\Receipt\GRNoneReceiptRepository;
use App\Service\StatusTransition\Traits\NoneReferenceReceiptSubscriberTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GRNoneReceiptRepository::class)
 */
class GRNoneReceipt extends Receipt
{
    use NoneReferenceReceiptSubscriberTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setType(ReceiptTypeDictionary::GOOD_RECEIPT);
    }

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $referenceId = null;

    /**
     * @return null
     */
    public function getReference()
    {
        return $this->referenceId;
    }

    /**
     * @Groups({"receipt.list", "receipt.read",})
     */
    public function getReferenceType(): string
    {
        return ReceiptReferenceTypeDictionary::GR_NONE;
    }
}
