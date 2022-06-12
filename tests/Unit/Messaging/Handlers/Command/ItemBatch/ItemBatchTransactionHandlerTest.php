<?php

namespace App\Tests\Unit\Messaging\Handlers\Command\ItemBatch;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Document\ItemBatchTransaction;
use App\Messaging\Handlers\Command\ItemBatch\ItemBatchTransactionHandler;
use App\Messaging\Messages\Command\ItemBatch\ItemBatchTransactionMessage;
use App\Tests\Unit\BaseUnitTestCase;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class ItemBatchTransactionHandlerTest extends BaseUnitTestCase
{
    protected DocumentManager|LegacyMockInterface|MockInterface|null $documentManagerMock;

    protected ?ItemBatchTransactionHandler $itemBatchHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentManagerMock = Mockery::mock(DocumentManager::class);
        $this->itemBatchHandler    = new ItemBatchTransactionHandler($this->documentManagerMock);
    }

    public function testItCanCallInvoke(): void
    {
        $this->documentManagerMock->shouldReceive('persist')
                                  ->once()
                                  ->with(Mockery::type(ItemBatchTransaction::class))
                                  ->andReturn();
        $this->documentManagerMock->shouldReceive('flush')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturn();

        $message = (new ItemBatchTransactionMessage())
            ->setItemBatchId(1)
            ->setReceiptId(1)
            ->setQuantity(5)
            ->setActionType(ItemTransactionActionTypeDictionary::STOW)
            ->setWarehouseId(10)
            ->setWarehouseStorageBinId(20)
            ->setUpdatedBy(1)
            ->setUpdatedAt(new DateTime());

        $this->itemBatchHandler->__invoke($message);
    }
}
