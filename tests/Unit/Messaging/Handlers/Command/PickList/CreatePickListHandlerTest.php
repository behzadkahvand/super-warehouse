<?php

namespace App\Tests\Unit\Messaging\Handlers\Command\PickList;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Receipt;
use App\Entity\ReceiptItem;
use App\Entity\Shipment;
use App\Entity\ShipmentItem;
use App\Events\PickList\ShipmentPickListCreatedEvent;
use App\Messaging\Handlers\Command\PickList\CreatePickListHandler;
use App\Messaging\Messages\Command\PickList\CreatePickListMessage;
use App\Repository\ShipmentRepository;
use App\Service\PickList\PickListService;
use App\Tests\Unit\BaseUnitTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CreatePickListHandlerTest extends BaseUnitTestCase
{
    public function testInvoke(): void
    {
        $shipmentRepository = Mockery::mock(ShipmentRepository::class);
        $pickListService    = Mockery::mock(PickListService::class);
        $dispatcher         = Mockery::mock(EventDispatcherInterface::class);
        $shipment           = Mockery::mock(Shipment::class);
        $receipt            = Mockery::mock(Receipt::class);
        $shipmentItem       = Mockery::mock(ShipmentItem::class);
        $manager            = Mockery::mock(EntityManagerInterface::class);

        $shipmentItem->shouldReceive('getReceiptItem')
                     ->once()
                     ->withNoArgs()
                     ->andReturn(new ReceiptItem());

        $receipt->shouldReceive('getStatus')
                ->once()
                ->withNoArgs()
                ->andReturn(ReceiptStatusDictionary::APPROVED);

        $shipment->shouldReceive('getReceipt')
                 ->once()
                 ->withNoArgs()
                 ->andReturn($receipt);

        $shipment->shouldReceive('getShipmentItems')
                 ->once()
                 ->withNoArgs()
                 ->andReturn(new ArrayCollection([$shipmentItem]));

        $shipmentRepository->shouldReceive('find')
                           ->once()
                           ->with(
                               Mockery::type('integer'),
                               Mockery::type('integer')
                           )
                           ->andReturn($shipment);

        $pickListService->shouldReceive('create')
                        ->once()
                        ->with(Mockery::type(ReceiptItem::class), Mockery::type('bool'))
                        ->andReturn([]);

        $dispatcher->shouldReceive('dispatch')
                   ->once()
                   ->with(Mockery::type(ShipmentPickListCreatedEvent::class))
                   ->andReturn(new stdClass());

        $manager->shouldReceive('beginTransaction')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $manager->shouldReceive('commit')
                ->once()
                ->withNoArgs()
                ->andReturn();

        $createPickListHandler = new CreatePickListHandler(
            $shipmentRepository,
            $pickListService,
            $dispatcher,
            $manager
        );
        $createPickListHandler->__invoke(new CreatePickListMessage(1));
    }
}
