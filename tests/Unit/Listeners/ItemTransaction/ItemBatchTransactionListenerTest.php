<?php

namespace App\Tests\Unit\Listeners\ItemTransaction;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Entity\Admin;
use App\Events\ItemTransaction\ItemBatchTransactionCreatedEvent;
use App\Listeners\ItemTransaction\ItemBatchTransactionListener;
use App\Messaging\Messages\Command\ItemBatch\ItemBatchTransactionMessage;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;

class ItemBatchTransactionListenerTest extends MockeryTestCase
{
    private ?MessageBusInterface $bus;

    private ?Security $security;

    private ?ItemBatchTransactionListener $itemTransactionListener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus                     = Mockery::mock(MessageBusInterface::class);
        $this->security                = Mockery::mock(Security::class);
        $this->itemTransactionListener = new ItemBatchTransactionListener(
            $this->bus,
            $this->security
        );
    }

    protected function tearDown(): void
    {
        $this->bus      = null;
        $this->security = null;
        unset($this->itemTransactionListener);
    }

    public function testItCanCallOnCreated(): void
    {
        $admin = Mockery::mock(Admin::class);
        $admin->shouldReceive('getUsername')
              ->withNoArgs()
              ->once()
              ->andReturn('tester1');

        $this->security->shouldReceive('getUser')
                       ->withNoArgs()
                       ->twice()
                       ->andReturn($admin);

        $this->bus->shouldReceive('dispatch')
                  ->once()
                  ->with(Mockery::type(ItemBatchTransactionMessage::class))
                  ->andReturn(new Envelope(new stdClass()));

        $event = new ItemBatchTransactionCreatedEvent(
            2,
            2,
            5,
            ItemTransactionActionTypeDictionary::RELOCATE,
            null,
            null
        );
        $this->itemTransactionListener->onCreated($event);
    }
}
