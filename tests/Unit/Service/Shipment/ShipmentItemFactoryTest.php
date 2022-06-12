<?php

namespace App\Tests\Unit\Service\Shipment;

use App\Entity\ShipmentItem;
use App\Service\Shipment\ShipmentItemFactory;
use App\Tests\Unit\BaseUnitTestCase;

class ShipmentItemFactoryTest extends BaseUnitTestCase
{
    public function testItCanCreateObject(): void
    {
        self::assertInstanceOf(ShipmentItem::class, (new ShipmentItemFactory())->create());
    }
}
