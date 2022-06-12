<?php

namespace App\Service\Receipt;

use App\Entity\Receipt\STInboundReceipt;

class STInboundReceiptFactory
{
    public function create(): STInboundReceipt
    {
        return new STInboundReceipt();
    }
}
