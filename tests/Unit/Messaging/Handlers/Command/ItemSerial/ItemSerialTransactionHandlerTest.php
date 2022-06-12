<?php

namespace App\Tests\Unit\Messaging\Handlers\Command\ItemSerial;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Document\ItemSerialTransaction;
use App\Messaging\Handlers\Command\ItemSerial\ItemSerialTransactionHandler;
use App\Messaging\Messages\Command\ItemSerial\ItemSerialTransactionMessage;
use App\Tests\Unit\BaseUnitTestCase;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class ItemSerialTransactionHandlerTest extends BaseUnitTestCase
{
    protected DocumentManager|LegacyMockInterface|MockInterface|null $documentManagerMock;

    protected ?ItemSerialTransactionHandler $itemSerialHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentManagerMock = Mockery::mock(DocumentManager::class);
        $this->itemSerialHandler   = new ItemSerialTransactionHandler($this->documentManagerMock);
    }

    public function testItCanCallInvoke(): void
    {
        $this->documentManagerMock->shouldReceive('persist')
                                  ->once()
                                  ->with(Mockery::type(ItemSerialTransaction::class))
                                  ->andReturn();
        $this->documentManagerMock->shouldReceive('flush')
                                  ->once()
                                  ->withNoArgs()
                                  ->andReturn();

        $message = (new ItemSerialTransactionMessage())
            ->setItemSerialId(1)
            ->setReceiptId(1)
            ->setActionType(ItemTransactionActionTypeDictionary::PICK)
            ->setWarehouseId(null)
            ->setWarehouseStorageBinId(null)
            ->setUpdatedBy(1)
            ->setUpdatedAt(new DateTime("now"));

        $this->itemSerialHandler->__invoke($message);
    }
}
