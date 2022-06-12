<?php

namespace App\Tests\Unit\Service\ReceiptItem;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\DTO\ReceiptItemData;
use App\Entity\Inventory;
use App\Entity\Receipt\STOutboundReceipt;
use App\Entity\ReceiptItem;
use App\Events\ReceiptItem\StoringReceiptItemManuallyEvent;
use App\Service\Receipt\ReceiptItemFactory;
use App\Service\ReceiptItem\ReceiptItemManualService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ReceiptItemManualServiceTest extends BaseUnitTestCase
{
    private LegacyMockInterface|EntityManagerInterface|MockInterface|null $entityManager;

    private EventDispatcherInterface|LegacyMockInterface|MockInterface|null $eventDispatcher;

    private LegacyMockInterface|ReceiptItemFactory|MockInterface|null $receiptItemFactory;

    private ?ReceiptItemManualService $receiptItemManual;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager      = Mockery::mock(EntityManagerInterface::class);
        $this->eventDispatcher    = Mockery::mock(EventDispatcherInterface::class);
        $this->receiptItemFactory = Mockery::mock(ReceiptItemFactory::class);

        $this->receiptItemManual = new ReceiptItemManualService(
            $this->entityManager,
            $this->eventDispatcher,
            $this->receiptItemFactory
        );
    }

    public function testItCanCreate(): void
    {
        $receiptItemData = (new ReceiptItemData())
            ->setQuantity(5)
            ->setReceipt(new STOutboundReceipt())
            ->setInventory(new Inventory())
            ->setReceiptType(ReceiptTypeDictionary::STOCK_TRANSFER);

        $receiptItem = Mockery::mock(ReceiptItem::class)->makePartial();

        $this->receiptItemFactory->shouldReceive('create')
                                 ->once()
                                 ->withNoArgs()
                                 ->andReturn($receiptItem);

        $this->eventDispatcher->shouldReceive('dispatch')
                              ->once()
                              ->with(Mockery::type(StoringReceiptItemManuallyEvent::class))
                              ->andReturn();

        $this->entityManager->shouldReceive('persist')
                            ->once()
                            ->with(Mockery::type(ReceiptItem::class))
                            ->andReturn();
        $this->entityManager->shouldReceive('flush')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        /** @var ReceiptItem $result */
        $result = $this->receiptItemManual->create($receiptItemData);

        self::assertEquals(ReceiptStatusDictionary::DRAFT, $result->getStatus());
        self::assertEquals($receiptItemData->getReceipt(), $result->getReceipt());
        self::assertEquals($receiptItemData->getInventory(), $result->getInventory());
        self::assertEquals($receiptItemData->getQuantity(), $result->getQuantity());
    }

    public function testItCanUpdate(): void
    {
        $receiptItemData = (new ReceiptItemData())
            ->setQuantity(5);

        $this->eventDispatcher->shouldReceive('dispatch')
                              ->once()
                              ->with(Mockery::type(StoringReceiptItemManuallyEvent::class))
                              ->andReturn();

        $this->entityManager->shouldReceive('flush')
                            ->once()
                            ->withNoArgs()
                            ->andReturn();

        $result = $this->receiptItemManual->update($receiptItemData, new ReceiptItem());

        self::assertEquals($receiptItemData->getQuantity(), $result->getQuantity());
    }
}
