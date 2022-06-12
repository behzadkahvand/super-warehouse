<?php

namespace App\Tests\Unit\Service\Integration\Timcheh\LogStore;

use App\Messaging\Messages\Event\Integration\Timcheh\LogStore\ProduceLogLifecycleMessage;
use App\Messaging\Stamps\Event\UniqueIdStamp;
use App\Service\Integration\Timcheh\LogStore\ErrorLogResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;

class ErrorLogResolverTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|MessageBusInterface|Mockery\MockInterface|null $bus;

    protected ErrorLogResolver|null $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = Mockery::mock(MessageBusInterface::class);
        $this->sut = new ErrorLogResolver($this->bus);
    }

    public function testSupport(): void
    {
        $envelop = Mockery::mock(Envelope::class);
        $stamp   = Mockery::mock(ErrorDetailsStamp::class);

        $envelop->shouldReceive("last")
                ->once()
                ->with(ErrorDetailsStamp::class)
                ->andReturn($stamp);

        self::assertTrue($this->sut->support($envelop));
    }

    public function testHandleLog(): void
    {
        $envelop       = Mockery::mock(Envelope::class);
        $uniqueIdStamp = Mockery::mock(UniqueIdStamp::class);

        $errorDetailsStamp = Mockery::mock(ErrorDetailsStamp::class);

        $envelop->shouldReceive("last")
                ->once()
                ->with(UniqueIdStamp::class)
                ->andReturn($uniqueIdStamp);

        $envelop->shouldReceive("last")
                ->once()
                ->with(ErrorDetailsStamp::class)
                ->andReturn($errorDetailsStamp);

        $uniqueIdStamp->shouldReceive("getMessageId")
                      ->once()
                      ->withNoArgs()
                      ->andReturn(1);

        $errorDetailsStamp->shouldReceive("getExceptionMessage")
                          ->once()
                          ->withNoArgs()
                          ->andReturn("exception");

        $this->bus->shouldReceive("dispatch")
                  ->once()
                  ->with(Mockery::type(ProduceLogLifecycleMessage::class))
                  ->andReturn($envelop);

        $this->sut->handleLog($envelop);

        self::assertEquals(10, $this->sut::getPriority());
    }
}
