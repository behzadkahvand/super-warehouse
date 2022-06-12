<?php

namespace App\Tests\Unit\Service\ItemSerial\Serial;

use App\Entity\ItemSerial;
use App\Service\ItemSerial\Serial\AddSerialService;
use App\Service\ItemSerial\Serial\ItemSerialListener;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class ItemSerialListenerTest extends MockeryTestCase
{
    protected LegacyMockInterface|MockInterface|AddSerialService|null $addSerialServiceMock;

    protected LegacyMockInterface|MockInterface|ItemSerial|null $itemSerialMock;

    protected ?ItemSerialListener $itemSerialListener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addSerialServiceMock = Mockery::mock(AddSerialService::class);
        $this->itemSerialMock       = Mockery::mock(ItemSerial::class);

        $this->itemSerialListener = new ItemSerialListener($this->addSerialServiceMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->addSerialServiceMock = null;
        $this->itemSerialMock       = null;
        $this->itemSerialListener   = null;

        Mockery::close();
    }

    public function testItCanNotAddSerialToItemSerial(): void
    {
        $this->itemSerialMock->shouldReceive('getSerial')
                             ->once()
                             ->withNoArgs()
                             ->andReturn('serial');
        $this->itemSerialMock->shouldNotReceive('getId');

        $this->addSerialServiceMock->shouldNotReceive('addOne');

        $this->itemSerialListener->onItemSerialPostPersist($this->itemSerialMock);
    }

    public function testItCanAddSerialToItemSerial(): void
    {
        $this->itemSerialMock->shouldReceive('getSerial')
                             ->once()
                             ->withNoArgs()
                             ->andReturnNull();

        $this->itemSerialMock->shouldReceive('getId')
                             ->once()
                             ->withNoArgs()
                             ->andReturn(12);

        $this->addSerialServiceMock->shouldReceive('addOne')
                                   ->once()
                                   ->with(12)
                                   ->andReturn();

        $this->itemSerialListener->onItemSerialPostPersist($this->itemSerialMock);
    }
}
