<?php

namespace App\DataFixtures;

use App\Dictionary\StorageBinTypeDictionary;
use App\Entity\Warehouse;
use App\Entity\WarehouseStorageArea;
use App\Entity\WarehouseStorageBin;
use App\Service\WarehouseStorageBin\AutoGenerate\HelperTrait;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WarehouseStorageBinFixtures extends Fixture implements DependentFixtureInterface
{
    use HelperTrait;

    public function load(ObjectManager $manager)
    {
        $aisle = $this->createWarehouseStorageBin(StorageBinTypeDictionary::AISLE);
        $this->addReference('warehouse_storage_bin_1', $aisle);

        $bay = $this->createWarehouseStorageBin(StorageBinTypeDictionary::BAY);
        $bay->setParent($this->getReference('warehouse_storage_bin_1'));
        $this->addReference('warehouse_storage_bin_2', $bay);

        $cell = $this->createWarehouseStorageBin(StorageBinTypeDictionary::CELL);
        $cell->setParent($this->getReference('warehouse_storage_bin_2'));
        $this->addReference('warehouse_storage_bin_3', $cell);

        $cellForRelocate = $this->createWarehouseStorageBin(StorageBinTypeDictionary::CELL, 'AA-A0-02', 200);
        $this->addReference('warehouse_storage_bin_4', $cellForRelocate);

        $manager->persist($aisle);
        $manager->persist($bay);
        $manager->persist($cell);
        $manager->persist($cellForRelocate);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
            WarehouseStorageAreaFixtures::class,
            AdminFixtures::class,
        ];
    }

    protected function createWarehouseStorageBin(
        string $type,
        string $serial = null,
        int $height = 100,
        int $weight = 100,
        int $width = 100,
        int $length = 100,
        int $quantity = 100,
        int $isActiveForStow = 1,
        int $isActiveForPick = 1
    ): WarehouseStorageBin {
        $warehouseStorageBin = new WarehouseStorageBin();
        $warehouseStorageBin->setWarehouse($this->getReference('warehouse_1'));
        $warehouseStorageBin->setWarehouseStorageArea($this->getReference('warehouse_storage_area_1'));
        $warehouseStorageBin->setType($type);
        $warehouseStorageBin->setSerial($this->makeSerial($type, $serial));
        $warehouseStorageBin->setHeightCapacity($height);
        $warehouseStorageBin->setWeightCapacity($weight);
        $warehouseStorageBin->setWidthCapacity($width);
        $warehouseStorageBin->setLengthCapacity($length);
        $warehouseStorageBin->setQuantityCapacity($quantity);
        $warehouseStorageBin->setIsActiveForStow($isActiveForStow);
        $warehouseStorageBin->setIsActiveForPick($isActiveForPick);
        $warehouseStorageBin->setCreatedBy($this->getReference('admin_1')->getEmail());
        $warehouseStorageBin->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $warehouseStorageBin->setUpdatedAt(new \DateTime());
        $warehouseStorageBin->setCreatedAt(new \DateTime());

        return $warehouseStorageBin;
    }

    private function makeSerial(string $type, ?string $serial = null)
    {
        /** @var Warehouse $warehouse */
        $warehouse = $this->getReference('warehouse_1');
        /** @var WarehouseStorageArea $warehouseStorageArea */
        $warehouseStorageArea = $this->getReference('warehouse_storage_area_1');

        if ($serial) {
            return $this->formatSerial($serial, $warehouse, $warehouseStorageArea);
        }

        if ($type === StorageBinTypeDictionary::AISLE) {
            return $this->formatSerial('AA', $warehouse, $warehouseStorageArea);
        } elseif ($type === StorageBinTypeDictionary::BAY) {
            return $this->formatSerial('AA-A0', $warehouse, $warehouseStorageArea);
        } else {
            return $this->formatSerial('AA-A0-01', $warehouse, $warehouseStorageArea);
        }
    }
}
