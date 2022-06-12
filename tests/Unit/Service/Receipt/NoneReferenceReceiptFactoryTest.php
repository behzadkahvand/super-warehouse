<?php

namespace App\Tests\Unit\Service\Receipt;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt\GINoneReceipt;
use App\Entity\Receipt\GRNoneReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use App\Service\Receipt\Exceptions\ReceiptTypeNotFoundException;
use App\Service\Receipt\NoneReferenceReceiptFactory;
use App\Tests\Unit\BaseUnitTestCase;

class NoneReferenceReceiptFactoryTest extends BaseUnitTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testItCanCreateNoneReceipt(string $type, string $instanceType): void
    {
        self::assertInstanceOf($instanceType, (new NoneReferenceReceiptFactory())->create($type));
    }

    public function testItHasAnExceptionOnCreatingNoneReceipt(): void
    {
        self::expectException(ReceiptTypeNotFoundException::class);

        (new NoneReferenceReceiptFactory())->create('test');
    }

    public function dataProvider(): array
    {
        $receiptTypes = ReceiptTypeDictionary::toArray();

        return [
            'GOOD_RECEIPT'   => [$receiptTypes['GOOD_RECEIPT'], GRNoneReceipt::class],
            'GOOD_ISSUE'     => [$receiptTypes['GOOD_ISSUE'], GINoneReceipt::class],
            'STOCK_TRANSFER' => [$receiptTypes['STOCK_TRANSFER'], STOutboundReceipt::class],
        ];
    }
}
