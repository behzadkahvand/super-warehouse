<?php

namespace App\Messaging\Messages\Event\Integration\Timcheh\Shipment;

use App\DTO\BaseDTO;

class TargetShipmentData extends BaseDTO implements ShipmentDataIntegrationInterface
{
    use OrderShipmentMessageDataTrait;
}
