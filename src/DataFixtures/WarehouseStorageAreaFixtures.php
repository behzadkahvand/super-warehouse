<?php

namespace App\DataFixtures;

use App\Dictionary\StorageAreaCapacityCheckMethodDictionary;
use App\Dictionary\StorageAreaStowingStrategyDictionary;
use App\Entity\WarehouseStorageArea;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WarehouseStorageAreaFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $warehouseStorageArea = new WarehouseStorageArea();
        $warehouseStorageArea->setWarehouse($this->getReference('warehouse_1'));
        $warehouseStorageArea->setTitle('test');
        $warehouseStorageArea->setIsActive(1);
        $warehouseStorageArea->setCapacityCheckMethod(StorageAreaCapacityCheckMethodDictionary::QUANTITY);
        $warehouseStorageArea->setStowingStrategy(StorageAreaStowingStrategyDictionary::NONE);
        $warehouseStorageArea->setCreatedBy($this->getReference('admin_1')->getEmail());
        $warehouseStorageArea->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $warehouseStorageArea->setUpdatedAt(new \DateTime());
        $warehouseStorageArea->setCreatedAt(new \DateTime());

        $this->addReference('warehouse_storage_area_1', $warehouseStorageArea);

        $manager->persist($warehouseStorageArea);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
            AdminFixtures::class,
        ];
    }
}
