<?php

namespace App\Tests\Unit\Messaging\Handlers\Command\ItemSerial;

use App\Entity\ItemSerial;
use App\Messaging\Handlers\Command\ItemSerial\AddSerialToItemSerialHandler;
use App\Messaging\Messages\Command\ItemSerial\AddSerialToItemSerial;
use App\Service\Utils\SerialGenerator\SerialGeneratorService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Psr\Log\LoggerInterface;

class AddSerialToItemSerialHandlerTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $em;

    protected SerialGeneratorService|LegacyMockInterface|MockInterface|null $serialGeneratorMock;

    protected LoggerInterface|LegacyMockInterface|MockInterface|null $loggerMock;

    protected LegacyMockInterface|MockInterface|ItemSerial|null $itemSerialMock;

    protected ?AddSerialToItemSerialHandler $addSerialToItemSerialHandler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em                  = Mockery::mock(EntityManagerInterface::class);
        $this->serialGeneratorMock = Mockery::mock(SerialGeneratorService::class);
        $this->loggerMock          = Mockery::mock(LoggerInterface::class);
        $this->itemSerialMock      = Mockery::mock(ItemSerial::class);

        $this->addSerialToItemSerialHandler = new AddSerialToItemSerialHandler(
            $this->em,
            $this->serialGeneratorMock
        );
    }

    public function testItCanNotAddSerialWhenItemSerialNotFound(): void
    {
        $itemSerialId          = 12;
        $addSerialToItemSerial = new AddSerialToItemSerial($itemSerialId);

        $this->em->shouldReceive('getReference')
                 ->once()
                 ->with(ItemSerial::class, $itemSerialId)
                 ->andReturnNull();

        $this->addSerialToItemSerialHandler->setLogger($this->loggerMock);

        $this->loggerMock->shouldReceive('error')
                         ->once()
                         ->with(sprintf('It can not add serial to item serial %d when item serial not exist!', $itemSerialId))
                         ->andReturn();

        $this->serialGeneratorMock->shouldNotReceive('encode');
        $this->em->shouldNotReceive('flush');

        $this->addSerialToItemSerialHandler->__invoke($addSerialToItemSerial);
    }

    public function testItCanAddSerialToItemSerial(): void
    {
        $itemSerialId          = 12;
        $addSerialToItemSerial = new AddSerialToItemSerial($itemSerialId);

        $this->em->shouldReceive('getReference')
                 ->once()
                 ->with(ItemSerial::class, $itemSerialId)
                 ->andReturn($this->itemSerialMock);
        $this->em->shouldReceive('flush')
                 ->once()
                 ->withNoArgs()
                 ->andReturn();

        $this->serialGeneratorMock->shouldReceive('encode')
                                  ->once()
                                  ->with($itemSerialId)
                                  ->andReturn('serial');

        $this->itemSerialMock->shouldReceive('setSerial')
                             ->once()
                             ->with('serial')
                             ->andReturn($this->itemSerialMock);

        $this->addSerialToItemSerialHandler->__invoke($addSerialToItemSerial);
    }
}
