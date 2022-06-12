<?php

namespace App\Tests\Unit\Service\Receipt;

use App\Entity\ReceiptItem;
use App\Service\Receipt\ReceiptItemFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ReceiptItemFactoryTest extends MockeryTestCase
{
    public function testCreate(): void
    {
        $receiptItemFactory = new ReceiptItemFactory();

        self::assertInstanceOf(ReceiptItem::class, $receiptItemFactory->create());
    }
}
