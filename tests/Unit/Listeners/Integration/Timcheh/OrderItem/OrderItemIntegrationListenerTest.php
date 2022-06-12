<?php

namespace App\Tests\Unit\Listeners\Integration\Timcheh\OrderItem;

use App\Dictionary\ReceiptStatusDictionary;
use App\Dictionary\ReceiptReferenceTypeDictionary;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\ShipmentItem;
use App\Listeners\Integration\Timcheh\OrderItem\OrderItemIntegrationListener;
use App\Messaging\Messages\Event\Integration\Timcheh\OrderItem\UpdateOrderItemInTimchehMessage;
use App\Repository\ShipmentItemRepository;
use App\Service\Integration\IntegrationablePropertiesDiscoverService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderItemIntegrationListenerTest extends BaseUnitTestCase
{
    protected ?MessageBusInterface $bus;

    protected ?ReceiptItem $receiptItem;

    protected ?IntegrationablePropertiesDiscoverService $discoverService;

    protected ?LifecycleEventArgs $lifecycleEventArgs;

    protected ?EntityManagerInterface $manager;

    protected ?UnitOfWork $unitOfWork;

    protected ?OrderItemIntegrationListener $listener;

    protected ?ShipmentItemRepository $shipmentItemRepository;

    protected ?ShipmentItem $shipmentItem;

    protected ?Receipt $receipt;

    public function setUp(): void
    {
        $this->bus = Mockery::mock(MessageBusInterface::class);
        $this->discoverService = Mockery::mock(IntegrationablePropertiesDiscoverService::class);
        $this->lifecycleEventArgs = Mockery::mock(LifecycleEventArgs::class);
        $this->manager = Mockery::mock(EntityManagerInterface::class);
        $this->shipmentItemRepository = Mockery::mock(ShipmentItemRepository::class);
        $this->unitOfWork = Mockery::mock(UnitOfWork::class);
        $this->listener = new OrderItemIntegrationListener(
            $this->bus,
            $this->discoverService,
            $this->shipmentItemRepository
        );
        $this->receiptItem = Mockery::mock(ReceiptItem::class);
        $this->receipt = Mockery::mock(Receipt::class);
        $this->shipmentItem = Mockery::mock(ShipmentItem::class);
    }

    public function testOnPostUpdate(): void
    {
        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->receiptItem), ['timcheh.order.item.update'])
            ->andReturns(['status']);

        $this->lifecycleEventArgs->expects('getEntityManager')
            ->withNoArgs()
            ->andReturns($this->manager);

        $this->bus->expects('dispatch')
            ->with(Mockery::type(UpdateOrderItemInTimchehMessage::class))
            ->andReturn(new Envelope(new \stdClass()));

        $this->manager->expects('getUnitOfWork')->withNoArgs()->andReturns($this->unitOfWork);

        $this->unitOfWork->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->unitOfWork->expects('getEntityChangeSet')
            ->with($this->receiptItem)
            ->andReturns(
                [
                    "status" => [
                        0 => 'APPROVED',
                        1 => 'PICKING',
                    ],
                ]
            );

        $this->shipmentItemRepository->expects('getPartialShipmentItemByReceiptItem')
            ->with($this->receiptItem)
            ->andReturn($this->shipmentItem);

        $this->shipmentItem->expects('getId')
            ->withNoArgs()
            ->andReturn(1);

        $this->receiptItem->expects('getReceipt')
            ->withNoArgs()
            ->andReturn($this->receipt);

        $this->receiptItem->expects('getStatus')
            ->withNoArgs()
            ->andReturn(ReceiptStatusDictionary::PICKING);

        $this->receipt->expects('getReferenceType')
            ->withNoArgs()
            ->andReturn(ReceiptReferenceTypeDictionary::GI_SHIPMENT);

        $this->listener->onPostUpdate($this->receiptItem, $this->lifecycleEventArgs);
    }

    public function testOnPostUpdateWithEmptyIntegrationableProperties(): void
    {
        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->receiptItem), ['timcheh.order.item.update'])
            ->andReturns([]);

        $this->listener->onPostUpdate($this->receiptItem, $this->lifecycleEventArgs);
    }

    public function testOnPostUpdateWithNotChangedIntegrationableProperties(): void
    {
        $this->discoverService->expects('getIntegrationableProperties')
            ->with(get_class($this->receiptItem), ['timcheh.order.item.update'])
            ->andReturns(["quantity"]);

        $this->lifecycleEventArgs->expects('getEntityManager')->withNoArgs()->andReturns($this->manager);

        $this->manager->expects('getUnitOfWork')->withNoArgs()->andReturns($this->unitOfWork);

        $this->unitOfWork->expects('computeChangeSets')->withNoArgs()->andReturns();
        $this->unitOfWork->expects('getEntityChangeSet')
            ->with($this->receiptItem)
            ->andReturns([]);

        $this->listener->onPostUpdate($this->receiptItem, $this->lifecycleEventArgs);
    }
}
