<?php

namespace App\Tests\Unit\Service\Receipt;

use App\Entity\Receipt\STInboundReceipt;
use App\Service\Receipt\STInboundReceiptFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class STInboundReceiptFactoryTest extends MockeryTestCase
{
    public function testCreate(): void
    {
        $receiptFactory = new STInboundReceiptFactory();

        self::assertInstanceOf(STInboundReceipt::class, $receiptFactory->create());
    }
}
