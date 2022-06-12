<?php

namespace App\DataFixtures;

use App\Dictionary\PickListBugReportStatusDictionary;
use App\Entity\PickListBugReport;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PickListBugReportFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $pickList = new PickListBugReport();
        $pickList->setQuantity(1);
        $pickList->setStatus(PickListBugReportStatusDictionary::PENDING);
        $pickList->setInventory($this->getReference('inventory_1'));
        $pickList->setPickList($this->getReference('pick_list_1'));
        $pickList->setWarehouse($this->getReference('warehouse_1'));

        $manager->persist($pickList);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PickListFixtures::class,
            WarehouseFixtures::class,
            InventoryFixtures::class,
        ];
    }
}
