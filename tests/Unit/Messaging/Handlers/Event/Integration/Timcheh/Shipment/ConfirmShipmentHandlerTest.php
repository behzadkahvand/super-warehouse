<?php

namespace App\Tests\Unit\Messaging\Handlers\Event\Integration\Timcheh\Shipment;

use App\Messaging\Handlers\Event\Integration\Timcheh\Shipment\ConfirmShipmentHandler;
use App\Messaging\Messages\Event\Integration\Timcheh\Shipment\ConfirmOrderShipmentMessage;
use App\Service\Shipment\Integration\ShipmentConfirmService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class ConfirmShipmentHandlerTest extends BaseUnitTestCase
{
    public function testItCanHandleCloneMessage(): void
    {
        $shipmentConfirmService = Mockery::mock(ShipmentConfirmService::class);

        $shipmentConfirmService->expects('__invoke')
                               ->with(1)
                               ->andReturns();

        $sut = new ConfirmShipmentHandler($shipmentConfirmService);

        $sut((new ConfirmOrderShipmentMessage())->setId(1));
    }
}
