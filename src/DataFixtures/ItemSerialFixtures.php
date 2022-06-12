<?php

namespace App\DataFixtures;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemSerial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ItemSerialFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->makeItem(
            'item_serial_1',
            'test',
            'item_batch_1',
            'warehouse_1',
            'warehouse_storage_bin_3',
            'inventory_1',
            ItemSerialStatusDictionary::SALABLE_STOCK,
            'admin_1'
        ));
        $manager->persist($this->makeItem(
            'item_serial_2',
            'test2',
            'item_batch_2',
            'warehouse_1',
            'warehouse_storage_bin_1',
            'inventory_1',
            ItemSerialStatusDictionary::SALABLE_STOCK,
            'admin_1'
        ));
        $manager->persist($this->makeItem(
            'item_serial_3',
            'test3',
            'item_batch_3',
            'warehouse_1',
            'warehouse_storage_bin_1',
            'inventory_1',
            ItemSerialStatusDictionary::SALABLE_STOCK,
            'admin_1'
        ));
        $manager->persist($this->makeItem(
            'item_serial_4',
            'test4',
            'item_batch_4',
            'warehouse_1',
            'warehouse_storage_bin_1',
            'inventory_1',
            ItemSerialStatusDictionary::SALABLE_STOCK,
            'admin_1'
        ));
        $manager->persist($this->makeItem(
            'item_serial_5',
            'test5',
            'item_batch_4',
            null,
            null,
            'inventory_1',
            ItemSerialStatusDictionary::OUT_OF_STOCK,
            'admin_1'
        ));
        $manager->persist($this->makeItem(
            'item_serial_6',
            'test6',
            'item_batch_4',
            null,
            null,
            'inventory_1',
            ItemSerialStatusDictionary::OUT_OF_STOCK,
            'admin_1'
        ));
        $manager->persist($this->makeItem(
            'item_serial_7',
            'test7',
            'item_batch_5',
            'warehouse_1',
            'warehouse_storage_bin_4',
            'inventory_1',
            ItemSerialStatusDictionary::SALABLE_STOCK,
            'admin_1'
        ));
        $manager->persist($this->makeItem(
            'item_serial_8',
            'test8',
            'item_batch_5',
            'warehouse_1',
            'warehouse_storage_bin_4',
            'inventory_1',
            ItemSerialStatusDictionary::SALABLE_STOCK,
            'admin_1'
        ));
        $manager->persist($this->makeItem(
            'item_serial_9',
            'test9',
            'item_batch_6',
            'warehouse_1',
            null,
            'inventory_1',
            ItemSerialStatusDictionary::OUT_OF_STOCK,
            'admin_1'
        ));
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ItemBatchFixtures::class,
            WarehouseStorageBinFixtures::class,
            WarehouseFixtures::class,
            InventoryFixtures::class,
            AdminFixtures::class,
        ];
    }

    private function makeItem(
        $refName,
        $serial,
        $batchRef,
        $warehouseRef,
        $binRef,
        $inventoryRef,
        $status,
        $adminRef
    ): ItemSerial {
        $itemSerial = new ItemSerial();
        $itemSerial->setItemBatch($this->getReference($batchRef));
        $itemSerial->setWarehouse($warehouseRef ? $this->getReference($warehouseRef) : null);
        $itemSerial->setWarehouseStorageBin($binRef ? $this->getReference($binRef) : null);
        $itemSerial->setInventory($this->getReference($inventoryRef));
        $itemSerial->setStatus($status);
        $itemSerial->setSerial($serial);
        $itemSerial->setUpdatedBy($this->getReference($adminRef)->getEmail());
        $itemSerial->setCreatedBy($this->getReference($adminRef)->getEmail());
        $itemSerial->setUpdatedAt(new \DateTime());
        $itemSerial->setCreatedAt(new \DateTime());

        $this->addReference($refName, $itemSerial);

        return $itemSerial;
    }
}
