<?php

namespace App\Tests\Unit\Service\Integration\Timcheh\LogStore;

use App\Messaging\Messages\Event\Integration\Timcheh\LogStore\ProduceLogLifecycleMessage;
use App\Messaging\Stamps\Event\UniqueIdStamp;
use App\Service\Integration\Timcheh\LogStore\SentToFailureLogResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;

class SentToFailurelLogResolverTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|MessageBusInterface|Mockery\MockInterface|null $bus;

    protected SentToFailureLogResolver|null $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = Mockery::mock(MessageBusInterface::class);
        $this->sut = new SentToFailureLogResolver($this->bus);
    }

    public function testSupport(): void
    {
        $envelop = Mockery::mock(Envelope::class);
        $stamp   = Mockery::mock(SentToFailureTransportStamp::class);

        $envelop->shouldReceive("last")
                ->once()
                ->with(SentToFailureTransportStamp::class)
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

        $this->bus->shouldReceive("dispatch")
                  ->once()
                  ->with(Mockery::type(ProduceLogLifecycleMessage::class))
                  ->andReturn($envelop);

        $this->sut->handleLog($envelop);

        self::assertEquals(15, $this->sut::getPriority());
    }
}
