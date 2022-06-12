<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Document\ItemSerialTransaction;
use App\Entity\Admin;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\Receipt;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageBin;
use App\Service\ItemsTransaction\ItemSerialTransactionLogService;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\ItemSerialTransactionLogResolver;
use App\Tests\Unit\BaseUnitTestCase;
use DateTimeInterface;
use Mockery;
use Symfony\Component\Security\Core\Security;

final class ItemSerialTransactionLogResolverTest extends BaseUnitTestCase
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
            $this->itemTransactionLogService,
            $this->security
        );
    }

    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

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

        $receipt = Mockery::mock(Receipt::class);
        $receipt->expects('getId')
                ->withNoArgs()
                ->andReturn(1);

        $pullListItem->expects('getReceipt')
                     ->withNoArgs()
                     ->andReturn($receipt);

        $this->itemTransactionLogService->expects("log")
                                        ->with(
                                            ItemTransactionActionTypeDictionary::STOW,
                                            1,
                                            1,
                                            1,
                                            1,
                                            1,
                                            Mockery::type(DateTimeInterface::class),
                                        )
                                        ->andReturn(Mockery::mock(ItemSerialTransaction::class));

        $this->resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(3, $this->resolver::getPriority());
    }
}
