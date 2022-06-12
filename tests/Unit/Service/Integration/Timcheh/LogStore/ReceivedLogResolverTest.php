<?php

namespace App\Tests\Unit\Service\Integration\Timcheh\LogStore;

use App\Messaging\Messages\Event\Integration\Timcheh\LogStore\ProduceLogLifecycleMessage;
use App\Messaging\Stamps\Event\UniqueIdStamp;
use App\Service\Integration\Timcheh\LogStore\ReceivedLogResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class ReceivedLogResolverTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|MessageBusInterface|Mockery\MockInterface|null $bus;

    protected ReceivedLogResolver|null $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = Mockery::mock(MessageBusInterface::class);
        $this->sut = new ReceivedLogResolver($this->bus);
    }

    public function testSupport(): void
    {
        $envelop = Mockery::mock(Envelope::class);
        $stamp   = Mockery::mock(ReceivedStamp::class);

        $envelop->shouldReceive("last")
                ->once()
                ->with(ReceivedStamp::class)
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

        self::assertEquals(5, $this->sut::getPriority());
    }
}
