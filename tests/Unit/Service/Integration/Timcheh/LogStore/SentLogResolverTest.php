<?php

namespace App\Tests\Unit\Service\Integration\Timcheh\LogStore;

use App\Dictionary\LogStoreStatusDictionary;
use App\Messaging\Stamps\Event\UniqueIdStamp;
use App\Service\Integration\Timcheh\LogStore\LogStoreService;
use App\Service\Integration\Timcheh\LogStore\SentLogResolver;
use App\Tests\Unit\BaseUnitTestCase;
use DateTime;
use DateTimeInterface;
use Mockery;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\SentStamp;

class SentLogResolverTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|LogStoreService|Mockery\MockInterface|null $logStoreService;

    protected SentLogResolver|null $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logStoreService = Mockery::mock(LogStoreService::class);
        $this->sut             = new SentLogResolver($this->logStoreService);
    }

    public function testSupport(): void
    {
        $envelop = Mockery::mock(Envelope::class);
        $stamp   = Mockery::mock(SentStamp::class);

        $envelop->shouldReceive("last")
                ->once()
                ->with(SentStamp::class)
                ->andReturn($stamp);

        self::assertTrue($this->sut->support($envelop));
    }

    public function testHandleLog(): void
    {
        $envelop       = Mockery::mock(Envelope::class);
        $uniqueIdStamp = Mockery::mock(UniqueIdStamp::class);

        $envelop->shouldReceive("last")
                ->once()
                ->with(UniqueIdStamp::class)
                ->andReturn($uniqueIdStamp);

        $uniqueIdStamp->shouldReceive("getMessageId")
                      ->once()
                      ->withNoArgs()
                      ->andReturn(1);

        $this->logStoreService->shouldReceive("log")
                              ->once()
                              ->with(1, LogStoreStatusDictionary::SENT, Mockery::type(DateTimeInterface::class))
                              ->andReturnNull();

        $this->sut->handleLog($envelop);

        self::assertEquals(1, $this->sut::getPriority());
    }
}
