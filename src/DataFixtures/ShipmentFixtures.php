<?php

namespace App\DataFixtures;

use App\Dictionary\ShipmentCategoryDictionary;
use App\Dictionary\ShipmentStatusDictionary;
use App\Entity\Shipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ShipmentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $shipment = new Shipment();
        $shipment->setId(1);
        $shipment->setStatus(ShipmentStatusDictionary::SENT);
        $shipment->setCategory(ShipmentCategoryDictionary::HEAVY);
        $shipment->setDeliveryDate(new \DateTime());

        $this->addReference('shipment_1', $shipment);

        $manager->persist($shipment);
        $manager->flush();
    }
}
