<?php

namespace App\Tests\Unit\Service\Relocate\Stowing;

use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\Relocate\Stowing\RelocateBinResolverInterface;
use App\Service\Relocate\Stowing\RelocateBinService;
use App\Service\Relocate\Stowing\RelocationItemBatchLogService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mockery;

class RelocateBinServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|Mockery\MockInterface|RelocateBinResolverInterface|null $resolver;

    protected Mockery\LegacyMockInterface|EntityManagerInterface|Mockery\MockInterface|null $entityManager;

    protected DocumentManager|Mockery\LegacyMockInterface|Mockery\MockInterface|null $documentManager;

    protected Mockery\LegacyMockInterface|RelocationItemBatchLogService|Mockery\MockInterface|null $batchLogService;

    protected RelocateBinService|null $relocateBinService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver        = Mockery::mock(RelocateBinResolverInterface::class);
        $this->entityManager   = Mockery::mock(EntityManagerInterface::class);
        $this->documentManager = Mockery::mock(DocumentManager::class);
        $this->batchLogService = Mockery::mock(RelocationItemBatchLogService::class);

        $this->relocateBinService = new RelocateBinService(
            [$this->resolver],
            $this->entityManager,
            $this->documentManager,
            $this->batchLogService
        );
    }

    public function testRelocateSuccess(): void
    {
        $sourceBin      = Mockery::mock(WarehouseStorageBin::class);
        $destinationBin = Mockery::mock(WarehouseStorageBin::class);

        $destinationBin->expects('checkIsActiveForStow')
                       ->withNoArgs()
                       ->andReturnTrue();

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

        $itemSerial = Mockery::mock(ItemSerial::class);
        $collection = new ArrayCollection([$itemSerial]);

        $sourceBin->expects('getItemSerials')
                  ->withNoArgs()
                  ->andReturn($collection);

        $this->resolver->expects('resolve')
                       ->with($destinationBin, $itemSerial)
                       ->andReturn();

        $this->batchLogService->expects('makeBinRelocateBatchLog')
                              ->with($destinationBin, $collection)
                              ->andReturn();

        $this->relocateBinService->relocate($sourceBin, $destinationBin);
    }

    public function testRelocateWhenFailed(): void
    {
        $sourceBin      = Mockery::mock(WarehouseStorageBin::class);
        $destinationBin = Mockery::mock(WarehouseStorageBin::class);

        $destinationBin->expects('checkIsActiveForStow')
                       ->withNoArgs()
                       ->andReturnTrue();

        $this->entityManager->expects('beginTransaction')
                            ->withNoArgs()
                            ->andReturn();
        $this->entityManager->expects('close')
                            ->withNoArgs()
                            ->andReturn();
        $this->entityManager->expects('rollback')
                            ->withNoArgs()
                            ->andReturn();

        $itemSerial = Mockery::mock(ItemSerial::class);
        $collection = new ArrayCollection([$itemSerial]);

        $sourceBin->expects('getItemSerials')
                  ->withNoArgs()
                  ->andReturn($collection);

        $this->resolver->expects('resolve')
                       ->with($destinationBin, $itemSerial)
                       ->andThrow(Exception::class);

        self::expectException(Exception::class);

        $this->relocateBinService->relocate($sourceBin, $destinationBin);
    }
}
