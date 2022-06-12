<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking;

use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Events\PickList\PickingCompletedEvent;
use App\Service\PickList\HandHeld\Picking\HandHeldPickingService;
use App\Service\PickList\HandHeld\Picking\PickingResolverInterface;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mockery;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class HandHeldPickingServiceTest extends BaseUnitTestCase
{
    protected EntityManagerInterface|null $manager;

    protected HandHeldPickingService|null $handHeldPickingService;

    protected PickingResolverInterface|Mockery\LegacyMockInterface|Mockery\MockInterface|null $resolver;

    protected DocumentManager|Mockery\LegacyMockInterface|Mockery\MockInterface|null $documentManager;

    private Mockery\LegacyMockInterface|EventDispatcherInterface|Mockery\MockInterface|null $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = Mockery::mock(PickingResolverInterface::class);

        $this->manager = Mockery::mock(EntityManagerInterface::class);

        $this->documentManager = Mockery::mock(DocumentManager::class);

        $this->dispatcher = Mockery::mock(EventDispatcherInterface::class);

        $this->handHeldPickingService = new HandHeldPickingService(
            [$this->resolver],
            $this->manager,
            $this->dispatcher,
            $this->documentManager
        );
    }

    public function testPickSuccess(): void
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

        $pickList   = Mockery::mock(PickList::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $this->resolver->expects('resolve')
                       ->with($pickList, $itemSerial)
                       ->andReturn();

        $this->dispatcher->shouldReceive('dispatch')
                   ->once()
                   ->with(Mockery::type(PickingCompletedEvent::class))
                   ->andReturn(new stdClass());

        $this->handHeldPickingService->pick($pickList, $itemSerial);
    }

    public function testPickWhenException(): void
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

        $pickList   = Mockery::mock(PickList::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $this->resolver->expects('resolve')
                       ->with($pickList, $itemSerial)
                       ->andThrow(Exception::class);

        self::expectException(Exception::class);

        $this->handHeldPickingService->pick($pickList, $itemSerial);
    }
}
