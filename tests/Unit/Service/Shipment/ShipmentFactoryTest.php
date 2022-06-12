<?php

namespace App\Tests\Unit\Service\Shipment;

use App\Entity\Shipment;
use App\Service\Shipment\ShipmentFactory;
use App\Tests\Unit\BaseUnitTestCase;

class ShipmentFactoryTest extends BaseUnitTestCase
{
    public function testItCanCreateObject(): void
    {
        self::assertInstanceOf(Shipment::class, (new ShipmentFactory())->create());
    }
}
