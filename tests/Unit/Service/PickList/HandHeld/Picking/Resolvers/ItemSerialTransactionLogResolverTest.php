<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Document\ItemSerialTransaction;
use App\Entity\Admin;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageBin;
use App\Service\ItemsTransaction\ItemSerialTransactionLogService;
use App\Service\PickList\HandHeld\Picking\Resolvers\ItemSerialTransactionLogResolver;
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
        $this->resolver                  = new ItemSerialTransactionLogResolver(
            $this->itemTransactionLogService,
            $this->security
        );
    }

    public function testItCanResolve(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);

        $pickList = Mockery::mock(PickList::class);

        $admin = Mockery::mock(Admin::class);
        $admin->expects('getId')
              ->withNoArgs()
              ->andReturn(1);

        $this->security->expects('getUser')
                       ->withNoArgs()
                       ->andReturn($admin);

        $itemSerial->expects('getId')->withNoArgs()->andReturn(1);

        $storageBin = Mockery::mock(WarehouseStorageBin::class);
        $storageBin->expects('getId')
                   ->withNoArgs()
                   ->andReturn(1);

        $pickList->expects('getStorageBin')
                 ->withNoArgs()
                 ->andReturn($storageBin);

        $warehouse = Mockery::mock(Warehouse::class);
        $warehouse->expects('getId')->withNoArgs()->andReturn(1);

        $pickList->expects('getWarehouse')
                 ->withNoArgs()
                 ->andReturn($warehouse);

        $receipt = Mockery::mock(Receipt::class);
        $receipt->expects('getId')
                ->withNoArgs()
                ->andReturn(1);

        $receiptItem = Mockery::mock(ReceiptItem::class);
        $receiptItem->expects('getReceipt')
                    ->withNoArgs()
                    ->andReturn($receipt);

        $pickList->expects('getReceiptItem')
                 ->withNoArgs()
                 ->andReturn($receiptItem);

        $this->itemTransactionLogService->expects("log")
                                        ->with(
                                            ItemTransactionActionTypeDictionary::PICK,
                                            1,
                                            1,
                                            1,
                                            1,
                                            1,
                                            Mockery::type(DateTimeInterface::class),
                                        )
                                        ->andReturn(Mockery::mock(ItemSerialTransaction::class));

        $this->resolver->resolve($pickList, $itemSerial);

        self::assertEquals(4, $this->resolver::getPriority());
    }
}
