<?php

namespace App\Tests\Unit\Service\Relocate\Stowing;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Document\ItemBatchTransaction;
use App\Entity\Admin;
use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageBin;
use App\Repository\ItemBatchTransactionRepository;
use App\Service\ItemsTransaction\ItemBatchTransactionLogService;
use App\Service\Relocate\Stowing\RelocationItemBatchLogService;
use App\Tests\Unit\BaseUnitTestCase;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Symfony\Component\Security\Core\Security;

class RelocationItemBatchLogServiceTest extends BaseUnitTestCase
{
    protected Mockery\LegacyMockInterface|Mockery\MockInterface|Security|null $security;

    protected Mockery\LegacyMockInterface|ItemBatchTransactionRepository|Mockery\MockInterface|null $itemBatchTransactionRepository;

    protected RelocationItemBatchLogService|null $relocateItemBatchLog;

    protected ItemBatchTransactionLogService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $transactionLogService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->security                       = Mockery::mock(Security::class);
        $this->itemBatchTransactionRepository = Mockery::mock(ItemBatchTransactionRepository::class);
        $this->transactionLogService          = Mockery::mock(ItemBatchTransactionLogService::class);

        $this->relocateItemBatchLog = new RelocationItemBatchLogService(
            $this->security,
            $this->itemBatchTransactionRepository,
            $this->transactionLogService
        );
    }

    public function testItCanMakeItemRelocateBatchLog(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemBatch  = Mockery::mock(ItemBatch::class);

        $admin = Mockery::mock(Admin::class);

        $this->security->expects('getUser')
                       ->withNoArgs()
                       ->andReturn($admin);

        $admin->shouldReceive('getId')
              ->times()
              ->withNoArgs()
              ->andReturn(1);

        $itemBatch->shouldReceive('getId')
                  ->times()
                  ->withNoArgs()
                  ->andReturn(1);

        $storageBin->shouldReceive('getId')
                   ->times()
                   ->withNoArgs()
                   ->andReturn(1);

        $this->itemBatchTransactionRepository->expects('findItemBatchRelocated')
                                             ->with(1, 1, 1)
                                             ->andReturnNull();

        $warehouse = Mockery::mock(Warehouse::class);
        $warehouse->expects('getId')->withNoArgs()->andReturn(1);

        $storageBin->expects('getWarehouse')
                   ->withNoArgs()
                   ->andReturn($warehouse);

        $ItemBatchTransaction = Mockery::mock(ItemBatchTransaction::class);

        $this->transactionLogService->expects("log")
                                    ->with(
                                        ItemTransactionActionTypeDictionary::RELOCATE,
                                        null,
                                        1,
                                        1,
                                        1,
                                        0,
                                        1,
                                        Mockery::type(DateTimeInterface::class),
                                        Mockery::type(DateTimeInterface::class),
                                    )
                                    ->andReturn($ItemBatchTransaction);

        $ItemBatchTransaction->expects('getQuantity')
                             ->withNoArgs()
                             ->andReturn(0);
        $ItemBatchTransaction->expects('setQuantity')
                             ->with(1)
                             ->andReturnSelf();
        $ItemBatchTransaction->expects('setUpdatedAt')
                             ->with(Mockery::type(DateTimeInterface::class))
                             ->andReturnSelf();

        $this->relocateItemBatchLog->makeItemRelocateBatchLog($storageBin, $itemBatch);
    }

    public function testItCanMakeBinRelocateBatchLog(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);

        $itemSerial = Mockery::mock(ItemSerial::class);
        $collection = new ArrayCollection([$itemSerial]);

        $admin = Mockery::mock(Admin::class);

        $this->security->expects('getUser')
                       ->withNoArgs()
                       ->andReturn($admin);

        $admin->shouldReceive('getId')
              ->once()
              ->withNoArgs()
              ->andReturn(1);

        $storageBin->shouldReceive('getId')
                   ->once()
                   ->withNoArgs()
                   ->andReturn(1);

        $itemBatch = Mockery::mock(ItemBatch::class);
        $itemBatch->expects('getId')->withNoArgs()->andReturn(1);

        $itemSerial->expects('getItemBatch')->withNoArgs()->andReturn($itemBatch);

        $warehouse = Mockery::mock(Warehouse::class);
        $warehouse->expects('getId')->withNoArgs()->andReturn(1);

        $storageBin->expects('getWarehouse')
                   ->withNoArgs()
                   ->andReturn($warehouse);

        $this->transactionLogService->expects("log")
                                    ->with(
                                        ItemTransactionActionTypeDictionary::RELOCATE,
                                        null,
                                        1,
                                        1,
                                        1,
                                        1,
                                        1,
                                        Mockery::type(DateTimeInterface::class),
                                        Mockery::type(DateTimeInterface::class),
                                    )
                                    ->andReturn(Mockery::mock(ItemBatchTransaction::class));

        $this->relocateItemBatchLog->makeBinRelocateBatchLog($storageBin, $collection);
    }
}
