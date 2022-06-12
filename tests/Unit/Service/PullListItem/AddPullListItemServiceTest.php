<?php

namespace App\Tests\Unit\Service\PullListItem;

use App\Dictionary\PullListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptTypeDictionary;
use App\DTO\AddPullListItemData;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\Warehouse;
use App\Service\PullListItem\AddPullListItemService;
use App\Service\PullListItem\Exceptions\InvalidReceiptItemStatusException;
use App\Service\PullListItem\Exceptions\InvalidReceiptItemWarehouseException;
use App\Service\PullListItem\Exceptions\PullListItemExistenceException;
use App\Service\PullListItem\PullListItemFactory;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class AddPullListItemServiceTest extends BaseUnitTestCase
{
    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $managerMock;

    protected LegacyMockInterface|EntityManagerInterface|MockInterface|null $factoryMock;

    protected PullList|LegacyMockInterface|MockInterface|null $pullListMock;

    protected LegacyMockInterface|PullListItem|MockInterface|null $pullListItemMock;

    protected LegacyMockInterface|Warehouse|MockInterface|null $warehouseMock;

    protected LegacyMockInterface|ReceiptItem|MockInterface|null $receiptItemMock;

    protected Receipt|LegacyMockInterface|MockInterface|null $receiptMock;

    protected ?AddPullListItemData $addData;

    protected ?AddPullListItemService $addPullListItemService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->managerMock      = Mockery::mock(EntityManagerInterface::class);
        $this->factoryMock      = Mockery::mock(PullListItemFactory::class);
        $this->pullListMock     = Mockery::mock(PullList::class);
        $this->pullListItemMock = Mockery::mock(PullListItem::class);
        $this->warehouseMock    = Mockery::mock(Warehouse::class);
        $this->receiptItemMock  = Mockery::mock(ReceiptItem::class);
        $this->receiptMock      = Mockery::mock(Receipt::class);

        $this->addData = (new AddPullListItemData())->setPullList($this->pullListMock)
                                                    ->setReceiptItems(
                                                        new ArrayCollection([
                                                            $this->receiptItemMock,
                                                            $this->receiptItemMock
                                                        ])
                                                    );

        $this->addPullListItemService = new AddPullListItemService(
            $this->managerMock,
            $this->factoryMock
        );
    }

    public function testItHasAnExceptionWhenReceiptItemStatusIsInvalid(): void
    {
        $this->managerMock->expects('beginTransaction')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('close')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('rollback')
                          ->withNoArgs()
                          ->andReturn();

        $this->pullListMock->expects('getWarehouse')
                           ->withNoArgs()
                           ->andReturn($this->warehouseMock);

        $this->warehouseMock->expects('getId')
                            ->withNoArgs()
                            ->andReturn(10);

        $this->receiptItemMock->expects('getStatus')
                              ->withNoArgs()
                              ->andReturn(ReceiptStatusDictionary::APPROVED);

        self::expectException(InvalidReceiptItemStatusException::class);
        self::expectExceptionCode(400);
        self::expectExceptionMessage('Only receipt item with READY_TO_STOW status allowed!');

        $this->addPullListItemService->perform($this->addData);
    }

    public function testItHasAnExceptionWhenReceiptTypeIsGoodReceiptAndReceiptItemWarehouseIsInvalid(): void
    {
        $this->managerMock->expects('beginTransaction')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('close')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('rollback')
                          ->withNoArgs()
                          ->andReturn();

        $this->pullListMock->expects('getWarehouse')
                           ->withNoArgs()
                           ->andReturn($this->warehouseMock);

        $this->warehouseMock->shouldReceive('getId')
                            ->twice()
                            ->withNoArgs()
                            ->andReturn(10, 12);

        $this->receiptItemMock->expects('getStatus')
                              ->withNoArgs()
                              ->andReturn(ReceiptStatusDictionary::READY_TO_STOW);
        $this->receiptItemMock->expects('getReceipt')
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);

        $this->receiptMock->expects('getType')
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::GOOD_RECEIPT);
        $this->receiptMock->expects('getSourceWarehouse')
                          ->withNoArgs()
                          ->andReturn($this->warehouseMock);

        self::expectException(InvalidReceiptItemWarehouseException::class);
        self::expectExceptionCode(400);
        self::expectExceptionMessage('Receipt item warehouse must be equals to pull list warehouse!');

        $this->addPullListItemService->perform($this->addData);
    }

    public function testItHasAnExceptionWhenReceiptTypeIsStockTransferAndReceiptItemWarehouseIsInvalid(): void
    {
        $this->managerMock->expects('beginTransaction')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('close')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('rollback')
                          ->withNoArgs()
                          ->andReturn();

        $this->pullListMock->expects('getWarehouse')
                           ->withNoArgs()
                           ->andReturn($this->warehouseMock);

        $this->warehouseMock->shouldReceive('getId')
                            ->twice()
                            ->withNoArgs()
                            ->andReturn(10, 12);

        $this->receiptItemMock->expects('getStatus')
                              ->withNoArgs()
                              ->andReturn(ReceiptStatusDictionary::READY_TO_STOW);
        $this->receiptItemMock->expects('getReceipt')
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);

        $this->receiptMock->expects('getType')
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::STOCK_TRANSFER);
        $this->receiptMock->expects('getDestinationWarehouse')
                          ->withNoArgs()
                          ->andReturn($this->warehouseMock);

        self::expectException(InvalidReceiptItemWarehouseException::class);
        self::expectExceptionCode(400);
        self::expectExceptionMessage('Receipt item warehouse must be equals to pull list warehouse!');

        $this->addPullListItemService->perform($this->addData);
    }

    public function testItHasAnExceptionWhenReceiptTypeIsGoodIssueAndReceiptItemWarehouseIsInvalid(): void
    {
        $this->managerMock->expects('beginTransaction')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('close')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('rollback')
                          ->withNoArgs()
                          ->andReturn();

        $this->pullListMock->expects('getWarehouse')
                           ->withNoArgs()
                           ->andReturn($this->warehouseMock);

        $this->warehouseMock->expects('getId')
                            ->withNoArgs()
                            ->andReturn(10);

        $this->receiptItemMock->expects('getStatus')
                              ->withNoArgs()
                              ->andReturn(ReceiptStatusDictionary::READY_TO_STOW);
        $this->receiptItemMock->expects('getReceipt')
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);

        $this->receiptMock->expects('getType')
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::GOOD_ISSUE);

        self::expectException(InvalidReceiptItemWarehouseException::class);
        self::expectExceptionCode(400);
        self::expectExceptionMessage('Receipt item warehouse must be equals to pull list warehouse!');

        $this->addPullListItemService->perform($this->addData);
    }

    public function testItHasAnExceptionWhenPullListItemExists(): void
    {
        $this->managerMock->expects('beginTransaction')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('close')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('rollback')
                          ->withNoArgs()
                          ->andReturn();

        $this->pullListMock->expects('getWarehouse')
                           ->withNoArgs()
                           ->andReturn($this->warehouseMock);

        $this->warehouseMock->shouldReceive('getId')
                            ->twice()
                            ->withNoArgs()
                            ->andReturn(10);

        $this->receiptItemMock->expects('getStatus')
                              ->withNoArgs()
                              ->andReturn(ReceiptStatusDictionary::READY_TO_STOW);
        $this->receiptItemMock->expects('getReceipt')
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);
        $this->receiptItemMock->expects('getPullListItem')
                              ->withNoArgs()
                              ->andReturn($this->pullListItemMock);

        $this->receiptMock->expects('getType')
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::GOOD_RECEIPT);
        $this->receiptMock->expects('getSourceWarehouse')
                          ->withNoArgs()
                          ->andReturn($this->warehouseMock);

        self::expectException(PullListItemExistenceException::class);
        self::expectExceptionCode(400);
        self::expectExceptionMessage('Pull list item is already exists for receipt item!');

        $this->addPullListItemService->perform($this->addData);
    }

    public function testItCanAddItemsToPullList(): void
    {
        $this->managerMock->expects('beginTransaction')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->shouldReceive('persist')
                          ->twice()
                          ->with($this->pullListItemMock)
                          ->andReturn();
        $this->managerMock->expects('flush')
                          ->withNoArgs()
                          ->andReturn();
        $this->managerMock->expects('commit')
                          ->withNoArgs()
                          ->andReturn();

        $this->pullListMock->expects('getWarehouse')
                           ->withNoArgs()
                           ->andReturn($this->warehouseMock);

        $this->warehouseMock->shouldReceive('getId')
                            ->times(3)
                            ->withNoArgs()
                            ->andReturn(10);

        $this->receiptItemMock->shouldReceive('getStatus')
                              ->twice()
                              ->withNoArgs()
                              ->andReturn(ReceiptStatusDictionary::READY_TO_STOW);
        $this->receiptItemMock->shouldReceive('getReceipt')
                              ->twice()
                              ->withNoArgs()
                              ->andReturn($this->receiptMock);
        $this->receiptItemMock->shouldReceive('getPullListItem')
                              ->twice()
                              ->withNoArgs()
                              ->andReturnNull();
        $this->receiptItemMock->shouldReceive('getQuantity')
                              ->twice()
                              ->withNoArgs()
                              ->andReturn(3, 5);

        $this->receiptMock->shouldReceive('getType')
                          ->twice()
                          ->withNoArgs()
                          ->andReturn(ReceiptTypeDictionary::GOOD_RECEIPT, ReceiptTypeDictionary::STOCK_TRANSFER);
        $this->receiptMock->expects('getSourceWarehouse')
                          ->withNoArgs()
                          ->andReturn($this->warehouseMock);
        $this->receiptMock->expects('getDestinationWarehouse')
                          ->withNoArgs()
                          ->andReturn($this->warehouseMock);

        $this->factoryMock->shouldReceive('getPullListItem')
                          ->twice()
                          ->withNoArgs()
                          ->andReturn($this->pullListItemMock);

        $this->pullListItemMock->shouldReceive('setReceiptItem')
                               ->twice()
                               ->with($this->receiptItemMock)
                               ->andReturnSelf();
        $this->pullListItemMock->shouldReceive('setReceipt')
                               ->twice()
                               ->with($this->receiptMock)
                               ->andReturnSelf();
        $this->pullListItemMock->expects('setQuantity')
                               ->with(3)
                               ->andReturnSelf();
        $this->pullListItemMock->expects('setQuantity')
                               ->with(5)
                               ->andReturnSelf();
        $this->pullListItemMock->expects('setRemainQuantity')
                               ->with(3)
                               ->andReturnSelf();
        $this->pullListItemMock->expects('setRemainQuantity')
                               ->with(5)
                               ->andReturnSelf();
        $this->pullListItemMock->shouldReceive('setPullList')
                               ->twice()
                               ->with($this->pullListMock)
                               ->andReturnSelf();
        $this->pullListItemMock->shouldReceive('setStatus')
                               ->twice()
                               ->with(PullListStatusDictionary::DRAFT)
                               ->andReturnSelf();

        $this->pullListMock->shouldReceive('addItem')
                           ->twice()
                           ->with($this->pullListItemMock)
                           ->andReturnSelf();

        $this->addPullListItemService->perform($this->addData);
    }
}
