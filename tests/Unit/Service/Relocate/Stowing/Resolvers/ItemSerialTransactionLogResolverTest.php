<?php

namespace App\Tests\Unit\Service\Relocate\Stowing\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Document\ItemSerialTransaction;
use App\Entity\Admin;
use App\Entity\ItemSerial;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageBin;
use App\Service\ItemsTransaction\ItemSerialTransactionLogService;
use App\Service\Relocate\Stowing\Resolvers\ItemSerialTransactionLogResolver;
use App\Tests\Unit\BaseUnitTestCase;
use DateTimeInterface;
use Mockery;
use Symfony\Component\Security\Core\Security;

class ItemSerialTransactionLogResolverTest extends BaseUnitTestCase
{
    protected ItemSerialTransactionLogResolver|null $resolver;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|Security|null $security;

    protected Mockery\LegacyMockInterface|ItemSerialTransactionLogService|Mockery\MockInterface|null $itemTransactionLogService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemTransactionLogService = Mockery::mock(ItemSerialTransactionLogService::class);
        $this->security                  = Mockery::mock(Security::class);

        $this->resolver = new ItemSerialTransactionLogResolver(
            $this->security,
            $this->itemTransactionLogService
        );
    }

    public function testItCanResolve(): void
    {
        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $itemSerial = Mockery::mock(ItemSerial::class);

        $admin = Mockery::mock(Admin::class);
        $admin->expects('getId')
              ->withNoArgs()
              ->andReturn(1);

        $this->security->expects('getUser')
                       ->withNoArgs()
                       ->andReturn($admin);

        $storageBin->expects('getId')->withNoArgs()->andReturn(1);

        $warehouse = Mockery::mock(Warehouse::class);
        $warehouse->expects('getId')->withNoArgs()->andReturn(1);

        $storageBin->expects('getWarehouse')
                   ->withNoArgs()
                   ->andReturn($warehouse);

        $itemSerial->expects('getId')
                   ->withNoArgs()
                   ->andReturn(1);

        $this->itemTransactionLogService->expects("log")
                                        ->with(
                                            ItemTransactionActionTypeDictionary::RELOCATE,
                                            null,
                                            1,
                                            1,
                                            1,
                                            1,
                                            Mockery::type(DateTimeInterface::class),
                                        )
                                        ->andReturn(Mockery::mock(ItemSerialTransaction::class));

        $this->resolver->resolve($storageBin, $itemSerial);

        self::assertEquals(10, $this->resolver::getPriority());
    }
}
