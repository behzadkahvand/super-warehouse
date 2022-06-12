<?php

namespace App\Tests\Unit\Messaging\Handlers\Event\Integration\Timcheh\Shipment;

use App\Messaging\Handlers\Event\Integration\Timcheh\Shipment\CloneShipmentHandler;
use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\CloneOrderShipmentMessage;
use App\Service\Shipment\DTO\ShipmentCloneData;
use App\Service\Shipment\Integration\ShipmentCloneService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class CloneShipmentHandlerTest extends BaseUnitTestCase
{
    public function testItCanHandleCloneMessage(): void
    {
        $shipmentCloneService = Mockery::mock(ShipmentCloneService::class);

        $shipmentCloneService->expects('__invoke')
                             ->with(Mockery::type(ShipmentCloneData::class))
                             ->andReturns();

        $sut = new CloneShipmentHandler($shipmentCloneService);

        $sut(new CloneOrderShipmentMessage());
    }
}
