<?php

namespace App\Tests\Unit\Messaging\Handlers\Event\Integration\Timcheh\Shipment;

use App\Messaging\Handlers\Event\Integration\Timcheh\Shipment\CreateShipmentHandler;
use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\CreateOrderShipmentMessage;
use App\Service\Shipment\DTO\ShipmentData;
use App\Service\Shipment\ShipmentUpsertService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class CreateShipmentHandlerTest extends BaseUnitTestCase
{
    public function testItCanHandleCreateMessage(): void
    {
        $shipmentUpsertService = Mockery::mock(ShipmentUpsertService::class);

        $shipmentUpsertService->expects('create')
                              ->with(Mockery::type(ShipmentData::class))
                              ->andReturns();

        $sut = new CreateShipmentHandler($shipmentUpsertService);

        $sut(new CreateOrderShipmentMessage());
    }
}
