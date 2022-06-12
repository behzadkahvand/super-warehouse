<?php

namespace App\DataFixtures;

use App\Entity\ReceiptItem;
use App\Entity\ShipmentItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ShipmentItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $shipmentItem = new ShipmentItem();
        $shipmentItem->setId(1);
        $shipmentItem->setInventory($this->getReference('inventory_1'));
        $shipmentItem->setShipment($this->getReference('shipment_1'));
        $shipmentItem->setReceiptItem($this->getReference('receipt_item_5'));
        $shipmentItem->setQuantity(1);

        $manager->persist($shipmentItem);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            InventoryFixtures::class,
            ShipmentFixtures::class,
            ReceiptItemFixtures::class,
        ];
    }
}
