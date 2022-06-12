<?php

namespace App\Tests\Unit\Messaging\Handlers\Event\Integration\Timcheh\ShipmentItem;

use App\Messaging\Handlers\Event\Integration\Timcheh\ShipmentItem\UpdateShipmentItemHandler;
use App\Messaging\Messages\Event\Integration\Timcheh\OrderItem\UpdateOrderItemInSuperWarehouseMessage;
use App\Service\Shipment\Integration\ShipmentItemUpdateService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class UpdateShipmentItemHandlerTest extends BaseUnitTestCase
{
    public function testItCanHandleUpdateMessage(): void
    {
        $service = Mockery::mock(ShipmentItemUpdateService::class);

        $service->expects('update')
            ->with(1, 1)
            ->andReturns();

        $message = Mockery::mock(UpdateOrderItemInSuperWarehouseMessage::class);
        $message->expects('getId')
            ->withNoArgs()
            ->andReturn(1);

        $message->expects('getQuantity')
            ->withNoArgs()
            ->andReturn(1);

        $handler = new UpdateShipmentItemHandler($service);
        $handler($message);
    }
}
