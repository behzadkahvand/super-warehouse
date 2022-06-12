<?php

namespace App\DataFixtures;

use App\Entity\Inventory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InventoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 3; $i++) {
            $inventory = $this->createInventory($i);
            $manager->persist($inventory);
        }

        $manager->flush();
    }

    private function createInventory(int $id): Inventory
    {
        $inventory = new Inventory();
        $inventory->setId($id);
        $inventory->setColor('red');
        $inventory->setGuarantee('red');
        $inventory->setSize('red');
        $inventory->setProduct($this->getReference('product_1'));

        $this->addReference('inventory_' . $id, $inventory);

        return $inventory;
    }

    public function getDependencies()
    {
        return [
            ProductFixtures::class,
        ];
    }
}
