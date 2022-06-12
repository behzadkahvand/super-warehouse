<?php

namespace App\Tests\Unit\Service\Receipt;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\DTO\NoneReferenceReceiptData;
use App\Entity\Receipt\STOutboundReceipt;
use App\Entity\Warehouse;
use App\Service\Receipt\NoneReferenceReceiptFactory;
use App\Service\Receipt\NoneReferenceReceiptService;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class NoneReferenceReceiptServiceTest extends MockeryTestCase
{
    private LegacyMockInterface|EntityManagerInterface|MockInterface|null $entityManager;

    private NoneReferenceReceiptService|null $noneReferenceReceiptService;

    private LegacyMockInterface|NoneReferenceReceiptFactory|MockInterface|null $factoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = Mockery::mock(EntityManagerInterface::class);
        $this->factoryMock   = Mockery::mock(NoneReferenceReceiptFactory::class);

        $this->noneReferenceReceiptService = new NoneReferenceReceiptService(
            $this->entityManager,
            $this->factoryMock
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager               = null;
        $this->noneReferenceReceiptService = null;
        $this->factoryMock                 = null;

        Mockery::close();
    }

    public function testItCanMakeReceipt(): void
    {
        $receiptData = (new NoneReferenceReceiptData())
            ->setDescription("test")
            ->setType(ReceiptTypeDictionary::STOCK_TRANSFER)
            ->setSourceWarehouse(new Warehouse())
            ->setDestinationWarehouse(new Warehouse());

        $receipt = Mockery::mock(STOutboundReceipt::class)->makePartial();

        $this->factoryMock->shouldReceive('create')
                          ->once()
                          ->with(ReceiptTypeDictionary::STOCK_TRANSFER)
                          ->andReturn($receipt);

        $this->entityManager->shouldReceive('persist')
                            ->once()
                            ->with($receipt)
                            ->andReturn();
        $this->entityManager->shouldReceive('flush')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        $result = $this->noneReferenceReceiptService->makeReceipt($receiptData);

        self::assertEquals(ReceiptStatusDictionary::DRAFT, $result->getStatus());
        self::assertEquals($receiptData->getDescription(), $result->getDescription());
        self::assertEquals($receiptData->getSourceWarehouse(), $result->getSourceWarehouse());
        self::assertEquals($receiptData->getDestinationWarehouse(), $result->getDestinationWarehouse());
    }

    public function testItCanUpdateReceipt(): void
    {
        $receiptData = (new NoneReferenceReceiptData())
            ->setDescription("test")
            ->setSourceWarehouse(new Warehouse())
            ->setDestinationWarehouse(new Warehouse());

        $receipt = new STOutboundReceipt();
        $this->entityManager->shouldReceive('flush')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        $result = $this->noneReferenceReceiptService->updateReceipt($receipt, $receiptData);

        self::assertEquals(ReceiptTypeDictionary::STOCK_TRANSFER, $result->getType());
        self::assertEquals($receiptData->getDestinationWarehouse(), $result->getDestinationWarehouse());
        self::assertEquals($receiptData->getSourceWarehouse(), $result->getSourceWarehouse());
        self::assertEquals($receiptData->getDescription(), $result->getDescription());
    }
}
