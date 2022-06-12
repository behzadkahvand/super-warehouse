<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking\Resolvers;

use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Entity\ReceiptItem;
use App\Entity\ReceiptItemBatch;
use App\Entity\ReceiptItemSerial;
use App\Service\ItemBatch\ReceiptItemBatchFactory;
use App\Service\ItemSerial\ReceiptItemSerialFactory;
use App\Service\PickList\HandHeld\Picking\Resolvers\InsertReceiptItemSerialAndBatchResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;

final class InsertReceiptItemSerialAndBatchResolverTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|Mockery\MockInterface|ReceiptItemSerialFactory|null $receiptItemSerialFactory;

    protected ReceiptItemBatchFactory|Mockery\LegacyMockInterface|Mockery\MockInterface|null $receiptItemBatchFactory;

    protected Mockery\LegacyMockInterface|EntityManagerInterface|Mockery\MockInterface|null $entityManager;

    protected InsertReceiptItemSerialAndBatchResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->receiptItemSerialFactory = Mockery::mock(ReceiptItemSerialFactory::class);
        $this->receiptItemBatchFactory  = Mockery::mock(ReceiptItemBatchFactory::class);
        $this->entityManager            = Mockery::mock(EntityManagerInterface::class);

        $this->resolver = new InsertReceiptItemSerialAndBatchResolver(
            $this->receiptItemSerialFactory,
            $this->receiptItemBatchFactory,
            $this->entityManager
        );
    }

    public function testItCanResolve(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);

        $receiptItem = Mockery::mock(ReceiptItem::class);

        $pickList = Mockery::mock(PickList::class);
        $pickList->shouldReceive('getReceiptItem')
                 ->twice()
                 ->withNoArgs()
                 ->andReturn($receiptItem);
        $itemBatch = Mockery::mock(ItemBatch::class);
        $itemSerial->shouldReceive('getItemBatch')
                 ->once()
                 ->withNoArgs()
                 ->andReturn($itemBatch);

        $receiptItemSerial = Mockery::mock(ReceiptItemSerial::class);
        $receiptItemSerial->shouldReceive('setReceiptItem')
                          ->once()
                          ->with($receiptItem)
                          ->andReturnSelf();
        $receiptItemSerial->shouldReceive('setItemSerial')
                          ->once()
                          ->with($itemSerial)
                          ->andReturnSelf();

        $this->receiptItemSerialFactory->shouldReceive('create')
                                       ->once()
                                       ->withNoArgs()
                                       ->andReturn($receiptItemSerial);

        $receiptItemBatch = Mockery::mock(ReceiptItemBatch::class);
        $receiptItemBatch->shouldReceive('setReceiptItem')
                         ->once()
                         ->with($receiptItem)
                         ->andReturnSelf();
        $receiptItemBatch->shouldReceive('setItemBatch')
                         ->once()
                         ->with($itemBatch)
                         ->andReturnSelf();

        $this->receiptItemBatchFactory->shouldReceive('create')
                                      ->once()
                                      ->withNoArgs()
                                      ->andReturn($receiptItemBatch);

        $this->entityManager->expects('persist')
                            ->with($receiptItemSerial)
                            ->andReturns();
        $this->entityManager->expects('persist')
                            ->with($receiptItemBatch)
                            ->andReturns();
        $this->entityManager->expects('flush')
                            ->withNoArgs()
                            ->andReturns();

        $this->resolver->resolve($pickList, $itemSerial);
    }

    public function testPriority(): void
    {
        self::assertEquals($this->resolver::getPriority(), 10);
    }
}
