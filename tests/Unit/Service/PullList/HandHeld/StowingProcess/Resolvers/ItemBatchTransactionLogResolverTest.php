<?php

namespace App\Tests\Unit\Service\PullList\HandHeld\StowingProcess\Resolvers;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Document\ItemBatchTransaction;
use App\Entity\Admin;
use App\Entity\ItemBatch;
use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\Receipt;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageBin;
use App\Repository\PullListRepository;
use App\Service\ItemsTransaction\ItemBatchTransactionLogService;
use App\Service\PullList\HandHeld\StowingProcess\Resolvers\ItemBatchTransactionLogResolver;
use App\Tests\Unit\BaseUnitTestCase;
use DateTimeInterface;
use Mockery;
use Symfony\Component\Security\Core\Security;

final class ItemBatchTransactionLogResolverTest extends BaseUnitTestCase
{
    protected ItemBatchTransactionLogResolver|null $resolver;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|Security|null $security;

    protected Mockery\LegacyMockInterface|Mockery\MockInterface|PullListRepository|null $pullListRepository;

    protected ItemBatchTransactionLogService|Mockery\LegacyMockInterface|Mockery\MockInterface|null $transactionLogService;


    protected function setUp(): void
    {
        parent::setUp();

        $this->security              = Mockery::mock(Security::class);
        $this->pullListRepository    = Mockery::mock(PullListRepository::class);
        $this->transactionLogService = Mockery::mock(ItemBatchTransactionLogService::class);

        $this->resolver = new ItemBatchTransactionLogResolver(
            $this->security,
            $this->pullListRepository,
            $this->transactionLogService
        );
    }

    public function testResolve(): void
    {
        $pullList     = Mockery::mock(PullList::class);
        $pullListItem = Mockery::mock(PullListItem::class);
        $itemSerial   = Mockery::mock(ItemSerial::class);
        $storageBin   = Mockery::mock(WarehouseStorageBin::class);

        $receipt = Mockery::mock(Receipt::class);
        $receipt->expects('getId')
                ->withNoArgs()
                ->andReturn(1);
        $receipt->expects('getStatus')
                ->withNoArgs()
                ->andReturn(ReceiptStatusDictionary::DONE);

        $pullListItem->expects('getReceipt')
                     ->withNoArgs()
                     ->andReturn($receipt);

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

        $itemBatch = Mockery::mock(ItemBatch::class);
        $itemBatch->expects('getId')
                  ->withNoArgs()
                  ->andReturn(1);

        $itemSerial->expects('getItemBatch')
                   ->withNoArgs()
                   ->andReturn($itemBatch);

        $this->pullListRepository->expects('getReceiptPullListItemsCount')
                                 ->with($receipt)
                                 ->andReturn(3);

        $this->transactionLogService->expects("log")
                                    ->with(
                                        ItemTransactionActionTypeDictionary::STOW,
                                        1,
                                        1,
                                        1,
                                        1,
                                        3,
                                        1,
                                        Mockery::type(DateTimeInterface::class),
                                        Mockery::type(DateTimeInterface::class),
                                    )
                                    ->andReturn(Mockery::mock(ItemBatchTransaction::class));

        $this->resolver->resolve($pullList, $pullListItem, $storageBin, $itemSerial);

        self::assertEquals(1, $this->resolver::getPriority());
    }
}
