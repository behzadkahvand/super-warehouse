<?php

namespace App\Tests\Unit\Messaging\Handlers\Event\Integration\Timcheh\ShipmentItem;

use App\Messaging\Handlers\Event\Integration\Timcheh\ShipmentItem\DeleteShipmentItemHandler;
use App\Messaging\Messages\Event\Integration\Timcheh\OrderItem\DeleteOrderItemMessage;
use App\Service\Shipment\Integration\ShipmentItemDeleteService;
use App\Tests\Unit\BaseUnitTestCase;
use Mockery;

class DeleteShipmentItemHandlerTest extends BaseUnitTestCase
{
    public function testItCanHandleDeleteMessage(): void
    {
        $service = Mockery::mock(ShipmentItemDeleteService::class);

        $service->expects('delete')
            ->with(1)
            ->andReturns();

        $handler = new DeleteShipmentItemHandler($service);

        $message = Mockery::mock(DeleteOrderItemMessage::class);
        $message->expects('getEntityId')
            ->withNoArgs()
            ->andReturn(1);

        $handler($message);
    }
}
