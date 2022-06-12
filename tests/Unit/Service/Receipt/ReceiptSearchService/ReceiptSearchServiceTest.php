<?php

namespace App\Tests\Unit\Service\Receipt\ReceiptSearchService;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\Receipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\Receipt\ReceiptSearchService\ReceiptSearchFactory;
use App\Service\Receipt\ReceiptSearchService\ReceiptSearchService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\QueryBuilder;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class ReceiptSearchServiceTest extends BaseUnitTestCase
{
    protected QueryBuilderFilterService|LegacyMockInterface|MockInterface|null $filterServiceMock;

    protected LegacyMockInterface|ReceiptSearchFactory|MockInterface|null $factoryMock;

    protected QueryBuilder|LegacyMockInterface|MockInterface|null $queryBuilderMock;

    protected ?ReceiptSearchService $receiptSearchSearch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filterServiceMock = Mockery::mock(QueryBuilderFilterService::class);
        $this->factoryMock       = Mockery::mock(ReceiptSearchFactory::class);
        $this->queryBuilderMock  = Mockery::mock(QueryBuilder::class);

        $this->receiptSearchSearch = new ReceiptSearchService($this->filterServiceMock, $this->factoryMock);
    }

    public function testItCanSearchReceiptWithReferenceIdAndReceiptTypeFilter(): void
    {
        $data = [
            'filter' => [
                'type'         => ReceiptTypeDictionary::GOOD_RECEIPT,
                'reference.id' => 1
            ]
        ];

        $this->factoryMock->expects('getResourceReceiptClass')
                          ->with(true, ReceiptTypeDictionary::GOOD_RECEIPT)
                          ->andReturn(GRMarketPlacePackageReceipt::class);

        $this->filterServiceMock->expects('filter')
                                ->with(GRMarketPlacePackageReceipt::class, $data)
                                ->andReturn($this->queryBuilderMock);

        $result = $this->receiptSearchSearch->perform($data);

        self::assertInstanceOf(QueryBuilder::class, $result);
    }

    public function testItCanSearchReceiptWithNoFilters(): void
    {
        $data = [];

        $this->factoryMock->expects('getResourceReceiptClass')
                          ->with(false, null)
                          ->andReturn(Receipt::class);

        $this->filterServiceMock->expects('filter')
                                ->with(Receipt::class, $data)
                                ->andReturn($this->queryBuilderMock);

        $result = $this->receiptSearchSearch->perform($data);

        self::assertInstanceOf(QueryBuilder::class, $result);
    }
}
