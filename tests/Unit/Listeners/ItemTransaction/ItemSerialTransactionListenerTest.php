<?php

namespace App\Tests\Unit\Listeners\ItemTransaction;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Entity\Admin;
use App\Events\ItemTransaction\ItemSerialTransactionCreatedEvent;
use App\Listeners\ItemTransaction\ItemSerialTransactionListener;
use App\Messaging\Messages\Command\ItemSerial\ItemSerialTransactionMessage;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;

class ItemSerialTransactionListenerTest extends MockeryTestCase
{
    protected Mockery\LegacyMockInterface|MessageBusInterface|Mockery\MockInterface|null $busMock;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|Security|null $securityMock;

    protected ItemSerialTransactionListener $itemTransactionListener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->busMock                 = Mockery::mock(MessageBusInterface::class);
        $this->securityMock            = Mockery::mock(Security::class);
        $this->itemTransactionListener = new ItemSerialTransactionListener(
            $this->busMock,
            $this->securityMock
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->busMock      = null;
        $this->securityMock = null;
        unset($this->itemTransactionListener);

        Mockery::close();
    }

    public function testItCanCallOnCreated(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->shouldReceive('getUsername')
              ->withNoArgs()
              ->once()
              ->andReturn('tester');

        $this->securityMock->shouldReceive('getUser')
                           ->withNoArgs()
                           ->twice()
                           ->andReturn($admin);

        $this->busMock->shouldReceive('dispatch')
                      ->once()
                      ->with(Mockery::type(ItemSerialTransactionMessage::class))
                      ->andReturn(new Envelope(new stdClass()));

        $event = new ItemSerialTransactionCreatedEvent(
            1,
            1,
            ItemTransactionActionTypeDictionary::RELOCATE,
            10,
            null
        );
        $this->itemTransactionListener->onCreated($event);
    }
}
