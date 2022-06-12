<?php

namespace App\Tests\Unit\Service\Receipt\ReceiptSearchService;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\Receipt\STInboundReceipt;
use App\Service\Receipt\Exceptions\ReceiptTypeNotFoundException;
use App\Service\Receipt\ReceiptSearchService\ReceiptSearchFactory;
use App\Tests\Unit\BaseUnitTestCase;

class ReceiptSearchFactoryTest extends BaseUnitTestCase
{
    protected ?ReceiptSearchFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ReceiptSearchFactory();
    }

    public function testItCanGetResourceReceiptClassWhenReferenceIsNotFiltered(): void
    {
        $result = $this->factory->getResourceReceiptClass(false, null);

        self::assertEquals(Receipt::class, $result);
    }

    public function testItCanGetResourceReceiptClassWhenReferenceIsFilteredAndReceiptTypeIsGoodReceipt(): void
    {
        $result = $this->factory->getResourceReceiptClass(true, ReceiptTypeDictionary::GOOD_RECEIPT);

        self::assertEquals(GRMarketPlacePackageReceipt::class, $result);
    }

    public function testItCanGetResourceReceiptClassWhenReferenceIsFilteredAndReceiptTypeIsGoodIssue(): void
    {
        $result = $this->factory->getResourceReceiptClass(true, ReceiptTypeDictionary::GOOD_ISSUE);

        self::assertEquals(GIShipmentReceipt::class, $result);
    }

    public function testItCanGetResourceReceiptClassWhenReferenceIsFilteredAndReceiptTypeIsStockTransfer(): void
    {
        $result = $this->factory->getResourceReceiptClass(true, ReceiptTypeDictionary::STOCK_TRANSFER);

        self::assertEquals(STInboundReceipt::class, $result);
    }

    public function testItHasAnExceptionWhenReceiptTypeIsInvalid(): void
    {
        $this->expectException(ReceiptTypeNotFoundException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('Receipt Type is invalid!');

        $this->factory->getResourceReceiptClass(true, 'invalid');
    }
}
