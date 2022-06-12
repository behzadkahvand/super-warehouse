<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Events\PullList\StowingCompletedEvent;
use App\Repository\ItemSerialRepository;
use App\Service\PullList\HandHeld\StowingProcess\StowingProcessService;
use App\Service\PullList\HandHeld\StowingProcess\StowingResolverInterface;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mockery;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class StowingProcessServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|StowingResolverInterface|Mockery\MockInterface|null $resolver;

    protected Mockery\LegacyMockInterface|EntityManagerInterface|Mockery\MockInterface|null $manager;

    protected Mockery\LegacyMockInterface|ItemSerialRepository|Mockery\MockInterface|null $itemSerialRepository;

    protected StowingProcessService|null $stowingProcessService;

    private Mockery\LegacyMockInterface|EventDispatcherInterface|Mockery\MockInterface|null $dispatcher;

    protected DocumentManager|Mockery\LegacyMockInterface|Mockery\MockInterface|null $documentManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = Mockery::mock(StowingResolverInterface::class);

        $this->manager = Mockery::mock(EntityManagerInterface::class);

        $this->dispatcher = Mockery::mock(EventDispatcherInterface::class);

        $this->itemSerialRepository = Mockery::mock(ItemSerialRepository::class);

        $this->documentManager = Mockery::mock(DocumentManager::class);

        $this->stowingProcessService = new StowingProcessService(
            [$this->resolver],
            $this->manager,
            $this->itemSerialRepository,
            $this->dispatcher,
            $this->documentManager
        );
    }

    public function testStowSuccess(): void
    {
        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturn();
        $this->manager->expects('flush')
                      ->withNoArgs()
                      ->andReturn();
        $this->manager->expects('commit')
                      ->withNoArgs()
                      ->andReturn();
        $this->documentManager->expects('flush')
                      ->withNoArgs()
                      ->andReturn();

        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);

        $this->dispatcher->shouldReceive('dispatch')
                         ->once()
                         ->with(Mockery::type(StowingCompletedEvent::class))
                         ->andReturn(new stdClass());

        $this->resolver->expects('resolve')
                       ->with($pullList, $pullListItem, $storageBin, $itemSerial)
                       ->andReturn();

        $this->stowingProcessService->stow($pullList, $pullListItem, $storageBin, $itemSerial);
    }

    public function testStowWhenException(): void
    {
        $this->manager->expects('beginTransaction')
                      ->withNoArgs()
                      ->andReturn();
        $this->manager->expects('close')
                      ->withNoArgs()
                      ->andReturn();
        $this->manager->expects('rollback')
                      ->withNoArgs()
                      ->andReturn();

        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);

        $this->resolver->expects('resolve')
                       ->with($pullList, $pullListItem, $storageBin, $itemSerial)
                       ->andThrow(Exception::class);

        self::expectException(Exception::class);

        $this->stowingProcessService->stow($pullList, $pullListItem, $storageBin, $itemSerial);
    }
}
