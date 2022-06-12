<?php

namespace App\Tests\Unit\Service\ItemSerial\Serial;

use App\Messaging\Messages\Command\ItemSerial\AddSerialToItemSerial;
use App\Service\ItemSerial\Serial\AddSerialService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class AddSerialServiceTest extends MockeryTestCase
{
    protected LegacyMockInterface|MockInterface|MessageBusInterface|null $messageBusMock;

    protected ?AddSerialService $addSerialService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageBusMock = Mockery::mock(MessageBusInterface::class);

        $this->addSerialService = new AddSerialService($this->messageBusMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->addSerialService = null;
        $this->messageBusMock   = null;

        Mockery::close();
    }

    public function testItCanDispatchOneProduct(): void
    {
        $this->messageBusMock->shouldReceive('dispatch')
                             ->once()
                             ->with(Mockery::type(AddSerialToItemSerial::class))
                             ->andReturn(new Envelope(new stdClass()));

        $this->addSerialService->addOne(1);
    }

    public function testItCanDispatchManyProducts(): void
    {
        $this->messageBusMock->shouldReceive('dispatch')
                             ->times(3)
                             ->with(Mockery::type(AddSerialToItemSerial::class))
                             ->andReturn(new Envelope(new stdClass()));

        $this->addSerialService->addMany([1, 2, 3]);
    }
}
