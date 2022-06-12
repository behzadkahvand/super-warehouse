<?php

namespace App\Messaging\Messages\Event\Integration\Timcheh\Shipment;

use App\DTO\BaseDTO;

class SourceShipmentData extends BaseDTO implements ShipmentDataIntegrationInterface
{
    use OrderShipmentMessageDataTrait;
}
