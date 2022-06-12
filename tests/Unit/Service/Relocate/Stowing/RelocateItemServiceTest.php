<?php

namespace App\Tests\Unit\Service\Relocate\Stowing;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\Relocate\Stowing\RelocateBinResolverInterface;
use App\Service\Relocate\Stowing\RelocateItemResolverInterface;
use App\Service\Relocate\Stowing\RelocateItemService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mockery;

class RelocateItemServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|Mockery\MockInterface|RelocateItemResolverInterface|null $resolver;

    protected Mockery\LegacyMockInterface|EntityManagerInterface|Mockery\MockInterface|null $entityManager;

    protected DocumentManager|Mockery\LegacyMockInterface|Mockery\MockInterface|null $documentManager;

    protected RelocateItemService|null $relocateItemService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver        = Mockery::mock(RelocateBinResolverInterface::class);
        $this->entityManager   = Mockery::mock(EntityManagerInterface::class);
        $this->documentManager = Mockery::mock(DocumentManager::class);

        $this->relocateItemService = new RelocateItemService(
            [$this->resolver],
            $this->entityManager,
            $this->documentManager
        );
    }

    public function testRelocateSuccess(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $this->entityManager->expects('beginTransaction')
                            ->withNoArgs()
                            ->andReturn();
        $this->entityManager->expects('flush')
                            ->withNoArgs()
                            ->andReturn();
        $this->entityManager->expects('commit')
                            ->withNoArgs()
                            ->andReturn();
        $this->documentManager->expects('flush')
                              ->withNoArgs()
                              ->andReturn();

        $this->resolver->expects('resolve')
                       ->with($storageBin, $itemSerial)
                       ->andReturn();

        $this->relocateItemService->relocate($storageBin, $itemSerial);
    }

    public function testRelocateWhenFailed(): void
    {
        $sourceBin  = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $this->entityManager->expects('beginTransaction')
                            ->withNoArgs()
                            ->andReturn();
        $this->entityManager->expects('close')
                            ->withNoArgs()
                            ->andReturn();
        $this->entityManager->expects('rollback')
                            ->withNoArgs()
                            ->andReturn();

        $this->resolver->expects('resolve')
                       ->with($sourceBin, $itemSerial)
                       ->andThrow(Exception::class);

        self::expectException(Exception::class);

        $this->relocateItemService->relocate($sourceBin, $itemSerial);
    }
}
