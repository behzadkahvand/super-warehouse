<?php

namespace App\Tests\Unit\Service\ItemBatch;

use App\Entity\ReceiptItemBatch;
use App\Service\ItemBatch\ReceiptItemBatchFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class ReceiptItemBatchFactoryTest extends MockeryTestCase
{
    public function testCreate(): void
    {
        $receiptItemBatchFactory = new ReceiptItemBatchFactory();

        self::assertInstanceOf(ReceiptItemBatch::class, $receiptItemBatchFactory->create());
    }
}
