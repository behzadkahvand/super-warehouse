<?php

namespace App\Tests\Unit\Service\PickList;

use App\DTO\ShipmentPickListData;
use App\Entity\Shipment;
use App\Messaging\Messages\Command\Async\AsyncMessage;
use App\Repository\ShipmentRepository;
use App\Service\PickList\ShipmentPickListService;
use DateTime;
use DateTimeInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ShipmentPickListServiceTest extends MockeryTestCase
{
    public function testCreate(): void
    {
        $shipmentRepository = Mockery::mock(ShipmentRepository::class);
        $bus                = Mockery::mock(MessageBusInterface::class);
        $data               = Mockery::mock(ShipmentPickListData::class);
        $shipment           = Mockery::mock(Shipment::class);

        $data->shouldReceive('getPromiseDateFrom')
             ->once()
             ->withNoArgs()
             ->andReturn(new DateTime('now'));
        $data->shouldReceive('getPromiseDateTo')
             ->once()
             ->withNoArgs()
             ->andReturn(new DateTime('now'));
        $data->shouldReceive('getQuantity')
             ->once()
             ->withNoArgs()
             ->andReturn(1);

        $shipment->shouldReceive('getId')
                 ->once()
                 ->withNoArgs()
                 ->andReturn(1);

        $shipmentRepository->shouldReceive('getSpecificQuantityOfShipmentsWithReadyToPickReceiptWithinDeliveryRange')
                           ->once()
                           ->with(
                               Mockery::type(DateTimeInterface::class),
                               Mockery::type(DateTimeInterface::class),
                               Mockery::type('integer')
                           )
                           ->andReturn([$shipment]);

        $bus->shouldReceive('dispatch')
            ->once()
            ->with(Mockery::type(AsyncMessage::class))
            ->andReturn(new Envelope(new stdClass()));

        $pickListService = new ShipmentPickListService(
            $shipmentRepository,
            $bus,
        );

        $pickListService->create($data);
    }
}
