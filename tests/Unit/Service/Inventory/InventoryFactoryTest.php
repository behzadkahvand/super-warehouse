<?php

namespace App\Tests\Unit\Service\Inventory;

use App\Entity\Inventory;
use App\Service\Inventory\InventoryFactory;
use App\Tests\Unit\BaseUnitTestCase;

class InventoryFactoryTest extends BaseUnitTestCase
{
    public function testItCanCreateInventoryObject(): void
    {
        self::assertInstanceOf(Inventory::class, (new InventoryFactory())->create());
    }
}
