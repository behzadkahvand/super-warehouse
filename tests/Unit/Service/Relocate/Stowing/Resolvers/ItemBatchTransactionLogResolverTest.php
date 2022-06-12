<?php

namespace App\Tests\Unit\Service\Relocate\Stowing\Resolvers;

use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\WarehouseStorageBin;
use App\Service\Relocate\Stowing\RelocationItemBatchLogService;
use App\Service\Relocate\Stowing\Resolvers\ItemBatchTransactionLogResolver;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class ItemBatchTransactionLogResolverTest extends BaseUnitTestCase
{
    protected RelocationItemBatchLogService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $service;

    protected ItemBatchTransactionLogResolver|null $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service  = Mockery::mock(RelocationItemBatchLogService::class);
        $this->resolver = new ItemBatchTransactionLogResolver($this->service);
    }

    public function testItCanResolve(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $itemBatch = Mockery::mock(ItemBatch::class);
        $itemSerial->expects('getItemBatch')
                   ->withNoArgs()
                   ->andReturn($itemBatch);

        $this->service->expects('makeItemRelocateBatchLog')
                      ->with($storageBin, $itemBatch)
                      ->andReturn();

        $this->resolver->resolve($storageBin, $itemSerial);

        self::assertEquals(7, $this->resolver::getPriority());
    }
}
