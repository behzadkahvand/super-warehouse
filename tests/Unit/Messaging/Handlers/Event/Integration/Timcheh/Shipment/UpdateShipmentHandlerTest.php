<?php

namespace App\Tests\Unit\Messaging\Handlers\Event\Integration\Timcheh\Shipment;

use App\Messaging\Handlers\Event\Integration\Timcheh\Shipment\UpdateShipmentHandler;
use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\UpdateOrderShipmentMessage;
use App\Service\Shipment\DTO\ShipmentData;
use App\Service\Shipment\ShipmentUpsertService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class UpdateShipmentHandlerTest extends BaseUnitTestCase
{
    public function testItCanHandleUpdateMessage(): void
    {
        $shipmentUpsertService = Mockery::mock(ShipmentUpsertService::class);

        $shipmentUpsertService->expects('update')
                              ->with(Mockery::type(ShipmentData::class))
                              ->andReturns();

        $sut = new UpdateShipmentHandler($shipmentUpsertService);

        $sut(new UpdateOrderShipmentMessage());
    }
}
