<?php

namespace App\DTO;

use App\Entity\Receipt\STOutboundReceipt;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class STInboundReceiptFormData
{
    /**
     * @Assert\NotBlank(groups={"store",})
     * @Groups({"store",})
     */
    private ?STOutboundReceipt $outboundReceipt = null;


    public function setOutboundReceipt(?STOutboundReceipt $outboundReceipt): self
    {
        $this->outboundReceipt = $outboundReceipt;

        return $this;
    }

    public function getOutboundReceipt(): ?STOutboundReceipt
    {
        return $this->outboundReceipt;
    }
}
