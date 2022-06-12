<?php

namespace App\DataFixtures;

use App\Dictionary\PickListPriorityDictionary;
use App\Dictionary\PickListStatusDictionary;
use App\Entity\PickList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PickListFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->makePickList(
            'pick_list_1',
            'receipt_item_5',
            'warehouse_storage_bin_1',
            'warehouse_storage_area_1',
            'admin_1',
            'warehouse_1',
            1,
            PickListStatusDictionary::WAITING_FOR_ACCEPT,
            PickListPriorityDictionary::HIGH
        ));

        $manager->persist($this->makePickList(
            'pick_list_2',
            'receipt_item_50',
            'warehouse_storage_bin_1',
            'warehouse_storage_area_1',
            null,
            'warehouse_1',
            1,
            PickListStatusDictionary::WAITING_FOR_ACCEPT,
            PickListPriorityDictionary::HIGH
        ));

        $manager->persist($this->makePickList(
            'pick_list_3',
            'receipt_item_51',
            'warehouse_storage_bin_1',
            'warehouse_storage_area_1',
            'admin_1',
            'warehouse_1',
            1,
            PickListStatusDictionary::PICKING,
            PickListPriorityDictionary::HIGH
        ));

        $manager->persist($this->makePickList(
            'pick_list_4',
            'receipt_item_3',
            'warehouse_storage_bin_1',
            'warehouse_storage_area_1',
            'admin_1',
            'warehouse_1',
            1,
            PickListStatusDictionary::WAITING_FOR_ACCEPT,
            PickListPriorityDictionary::HIGH
        ));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ReceiptItemFixtures::class,
            AdminFixtures::class,
            WarehouseStorageBinFixtures::class,
            WarehouseStorageAreaFixtures::class,
            WarehouseFixtures::class,
        ];
    }

    private function makePickList(
        $refName,
        $receiptItemRef,
        $binRef,
        $areaRef,
        $pickerRef,
        $warehouseRef,
        $quantity,
        $status,
        $priority
    ): PickList {
        $pickList = new PickList();
        $pickList->setReceiptItem($this->getReference($receiptItemRef));
        $pickList->setStorageBin($this->getReference($binRef));
        $pickList->setStorageArea($this->getReference($areaRef));
        $pickList->setPicker($pickerRef ? $this->getReference($pickerRef) : null);
        $pickList->setWarehouse($this->getReference($warehouseRef));
        $pickList->setQuantity($quantity);
        $pickList->setStatus($status);
        $pickList->setPriority($priority);

        $this->addReference($refName, $pickList);

        return $pickList;
    }
}
