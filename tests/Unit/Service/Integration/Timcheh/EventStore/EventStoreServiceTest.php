<?php

namespace App\Tests\Unit\Service\Integration\Timcheh\EventStore;

use App\Document\Integration\Timcheh\EventStore;
use App\Entity\Transaction;
use App\Service\Integration\Timcheh\EventStore\EventStoreData;
use App\Service\Integration\Timcheh\EventStore\EventStoreFactory;
use App\Service\Integration\Timcheh\EventStore\EventStoreService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Mockery;

class EventStoreServiceTest extends BaseUnitTestCase
{
    protected DocumentManager|Mockery\LegacyMockInterface|Mockery\MockInterface|null $documentManager;

    protected EventStoreFactory|Mockery\LegacyMockInterface|Mockery\MockInterface|null $eventStoreFactory;

    protected EventStoreService|null $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentManager   = Mockery::mock(DocumentManager::class);
        $this->eventStoreFactory = Mockery::mock(EventStoreFactory::class);
        $this->sut = new EventStoreService($this->documentManager, $this->eventStoreFactory);
    }

    public function testItCanAppend(): void
    {
        $dto = (new EventStoreData())
            ->setMessageId(1)
            ->setCreatedAt(new \DateTime())
            ->setPayload(['data' => "1"])
            ->setSourceServiceName("TIMCHEH")
            ->setMessageName("test");

        $eventStore = Mockery::mock(EventStore::class);
        $eventStore->shouldReceive("setMessageId")
                   ->once()
                   ->with($dto->getMessageId())
                   ->andReturnSelf();
        $eventStore->shouldReceive("setMessageName")
                   ->once()
                   ->with($dto->getMessageName())
                   ->andReturnSelf();
        $eventStore->shouldReceive("setSourceServiceName")
                   ->once()
                   ->with($dto->getSourceServiceName())
                   ->andReturnSelf();
        $eventStore->shouldReceive("setPayload")
                   ->once()
                   ->with($dto->getPayload())
                   ->andReturnSelf();
        $eventStore->shouldReceive("setCreatedAt")
                   ->once()
                   ->with($dto->getCreatedAt())
                   ->andReturnSelf();

        $this->eventStoreFactory->shouldReceive("create")
                                ->once()
                                ->andReturn($eventStore);

        $this->documentManager->shouldReceive('persist')
                              ->once()
                              ->with($eventStore)
                              ->andReturn();

        $this->documentManager->shouldReceive('flush')
                              ->once()
                              ->withNoArgs()
                              ->andReturn();

        $this->sut->append($dto);
    }
}
