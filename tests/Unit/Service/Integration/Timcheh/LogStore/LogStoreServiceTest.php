<?php

namespace App\Tests\Unit\Service\Integration\Timcheh\LogStore;

use App\Dictionary\LogStoreStatusDictionary;
use App\Document\Integration\Timcheh\LogStore;
use App\Service\Integration\Timcheh\LogStore\LogStoreFactory;
use App\Service\Integration\Timcheh\LogStore\LogStoreService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Mockery;

class LogStoreServiceTest extends BaseUnitTestCase
{
    protected LogStoreService|null $sut;

    protected DocumentManager|Mockery\LegacyMockInterface|Mockery\MockInterface|null $documentManager;

    protected Mockery\LegacyMockInterface|LogStoreFactory|Mockery\MockInterface|null $logStoreFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentManager = Mockery::mock(DocumentManager::class);
        $this->logStoreFactory = Mockery::mock(LogStoreFactory::class);
        $this->sut             = new LogStoreService($this->documentManager, $this->logStoreFactory);
    }

    public function testItCanAppend(): void
    {
        $messageId  = 1;
        $createdAt  = new \DateTime();
        $status     = LogStoreStatusDictionary::PROCESSING;
        $resultCode = null;
        $resultMsg  = "test";

        $logStore = Mockery::mock(LogStore::class);
        $logStore->shouldReceive("setMessageId")
                 ->once()
                 ->with($messageId)
                 ->andReturnSelf();
        $logStore->shouldReceive("setStatus")
                 ->once()
                 ->with($status)
                 ->andReturnSelf();
        $logStore->shouldReceive("setResultCode")
                 ->once()
                 ->with($resultCode)
                 ->andReturnSelf();
        $logStore->shouldReceive("setResultMessage")
                 ->once()
                 ->with($resultMsg)
                 ->andReturnSelf();
        $logStore->shouldReceive("setCreatedAt")
                 ->once()
                 ->with($createdAt)
                 ->andReturnSelf();

        $this->logStoreFactory->shouldReceive("create")
                              ->once()
                              ->andReturn($logStore);

        $this->documentManager->shouldReceive('persist')
                              ->once()
                              ->with($logStore)
                              ->andReturn();

        $this->documentManager->shouldReceive('flush')
                              ->once()
                              ->withNoArgs()
                              ->andReturn();

        $this->sut->log($messageId, $status, $createdAt, $resultCode, $resultMsg);
    }
}
