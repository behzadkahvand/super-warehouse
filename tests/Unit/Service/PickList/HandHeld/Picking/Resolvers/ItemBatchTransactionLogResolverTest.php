<?php

namespace App\Tests\Unit\Service\PickList\HandHeld\Picking\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Document\ItemBatchTransaction;
use App\Entity\Admin;
use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\PickList;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageBin;
use App\Repository\PickListRepository;
use App\Service\ItemsTransaction\ItemBatchTransactionLogService;
use App\Service\PickList\HandHeld\Picking\Resolvers\ItemBatchTransactionLogResolver;
use App\Tests\Unit\BaseUnitTestCase;
use DateTimeInterface;
use Mockery;
use Symfony\Component\Security\Core\Security;

final class ItemBatchTransactionLogResolverTest extends BaseUnitTestCase
{
    protected ItemBatchTransactionLogResolver|null $resolver;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|Security|null $security;

    protected Mockery\LegacyMockInterface|PickListRepository|Mockery\MockInterface|null $pickListRepository;

    protected ItemBatchTransactionLogService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $transactionLogService;

    protected function setUp(): void
    {

        parent::setUp();

        $this->security              = Mockery::mock(Security::class);
        $this->pickListRepository    = Mockery::mock(PickListRepository::class);
        $this->transactionLogService = Mockery::mock(ItemBatchTransactionLogService::class);

        $this->resolver = new ItemBatchTransactionLogResolver(
            $this->security,
            $this->pickListRepository,
            $this->transactionLogService
        );
    }

    public function testItCanResolve(): void
    {
        $itemSerial = Mockery::mock(ItemSerial::class);
        $pickList   = Mockery::mock(PickList::class);

        $receipt = Mockery::mock(Receipt::class);
        $receipt->expects('getId')
                ->withNoArgs()
                ->andReturn(1);
        $receipt->expects('getStatus')
                ->withNoArgs()
                ->andReturn(ReceiptStatusDictionary::DONE);

        $receiptItem = Mockery::mock(ReceiptItem::class);
        $receiptItem->expects('getReceipt')
                    ->withNoArgs()
                    ->andReturn($receipt);

        $pickList->expects('getReceiptItem')
                 ->withNoArgs()
                 ->andReturn($receiptItem);

        $admin = Mockery::mock(Admin::class);
        $admin->expects('getId')
              ->withNoArgs()
              ->andReturn(1);

        $this->security->expects('getUser')
                       ->withNoArgs()
                       ->andReturn($admin);

        $itemBatch = Mockery::mock(ItemBatch::class);
        $itemBatch->expects('getId')->withNoArgs()->andReturn(1);

        $itemSerial->expects('getItemBatch')->withNoArgs()->andReturn($itemBatch);

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

        $this->pickListRepository->expects('getReceiptPickListsCount')
                                 ->with($receipt)
                                 ->andReturn(5);

        $this->transactionLogService->expects("log")
                                    ->with(
                                        ItemTransactionActionTypeDictionary::PICK,
                                        1,
                                        1,
                                        1,
                                        1,
                                        5,
                                        1,
                                        Mockery::type(DateTimeInterface::class),
                                        Mockery::type(DateTimeInterface::class),
                                    )
                                    ->andReturn(Mockery::mock(ItemBatchTransaction::class));

        $this->resolver->resolve($pickList, $itemSerial);

        self::assertEquals(2, $this->resolver::getPriority());
    }
}
