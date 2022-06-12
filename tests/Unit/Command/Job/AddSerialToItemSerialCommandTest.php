<?php

namespace App\Tests\Unit\Command\Job;

use App\Command\Job\AddSerialToItemSerialCommand;
use App\Repository\ItemSerialRepository;
use App\Service\ItemSerial\Serial\AddSerialService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AddSerialToItemSerialCommandTest extends MockeryTestCase
{
    protected Mockery\LegacyMockInterface|AddSerialService|Mockery\MockInterface|null $addSerialMock;

    protected Mockery\LegacyMockInterface|ItemSerialRepository|Mockery\MockInterface|null $itemSerialRepoMock;

    protected CommandTester $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->addSerialMock      = Mockery::mock(AddSerialService::class);
        $this->itemSerialRepoMock = Mockery::mock(ItemSerialRepository::class);

        $application = new Application();
        $application->add(new AddSerialToItemSerialCommand(
            $this->addSerialMock,
            $this->itemSerialRepoMock
        ));

        $command = $application->find('timcheh-warehouse:job:add-serial-to-item-serial');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->commandTester);

        $this->addSerialMock      = null;
        $this->itemSerialRepoMock = null;

        Mockery::close();
    }

    public function testItCanExecute(): void
    {
        $serialItemIds = [3, 5, 9, 13];

        $this->itemSerialRepoMock->shouldReceive('getItemSerialIdsHasNoSerial')
                                 ->once()
                                 ->withNoArgs()
                                 ->andReturn($serialItemIds);

        $this->addSerialMock->shouldReceive('addMany')
                            ->once()
                            ->with($serialItemIds)
                            ->andReturn();

        $this->commandTester->execute([]);
    }
}
