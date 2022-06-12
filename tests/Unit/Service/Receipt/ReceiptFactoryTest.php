<?php

namespace App\Tests\Unit\Service\Receipt;

use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Entity\Receipt\GINoneReceipt;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\Receipt\GRNoneReceipt;
use App\Entity\Receipt\STInboundReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use App\Service\Receipt\Exceptions\ReferenceTypeNotFoundException;
use App\Service\Receipt\ReceiptFactory;
use App\Tests\Unit\BaseUnitTestCase;

class ReceiptFactoryTest extends BaseUnitTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testItCanCreateReceipt(string $type, string $instanceType): void
    {
        self::assertInstanceOf($instanceType, (new ReceiptFactory())->create($type));
    }

    public function testItHasAnExceptionOnCreatingReceipt(): void
    {
        self::expectException(ReferenceTypeNotFoundException::class);

        (new ReceiptFactory())->create('test');
    }

    public function dataProvider(): array
    {
        $referenceTypes = ReceiptReferenceTypeDictionary::toArray();

        return [
            'GI_NONE'       => [$referenceTypes['GI_NONE'], GINoneReceipt::class],
            'GI_SHIPMENT'   => [$referenceTypes['GI_SHIPMENT'], GIShipmentReceipt::class],
            'GR_MP_PACKAGE' => [$referenceTypes['GR_MP_PACKAGE'], GRMarketPlacePackageReceipt::class],
            'GR_NONE'       => [$referenceTypes['GR_NONE'], GRNoneReceipt::class],
            'ST_INBOUND'    => [$referenceTypes['ST_INBOUND'], STInboundReceipt::class],
            'ST_OUTBOUND'   => [$referenceTypes['ST_OUTBOUND'], STOutboundReceipt::class],
        ];
    }
}
