<?php

namespace App\DataFixtures;

use App\Entity\ItemBatch;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ItemBatchFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->makeItemBatch(
            'item_batch_1',
            'receipt_1',
            'inventory_1',
            2,
            'admin_1'
        ));
        $manager->persist($this->makeItemBatch(
            'item_batch_2',
            'receipt_6',
            'inventory_1',
            1,
            'admin_1'
        ));
        $manager->persist($this->makeItemBatch(
            'item_batch_3',
            'receipt_7',
            'inventory_1',
            1,
            'admin_1'
        ));
        $manager->persist($this->makeItemBatch(
            'item_batch_4',
            'receipt_8',
            'inventory_1',
            2,
            'admin_1'
        ));
        $manager->persist($this->makeItemBatch(
            'item_batch_5',
            'receipt_9',
            'inventory_1',
            2,
            'admin_1'
        ));
        $manager->persist($this->makeItemBatch(
            'item_batch_6',
            'receipt_10',
            'inventory_1',
            1,
            'admin_1'
        ));
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ReceiptFixtures::class,
            InventoryFixtures::class,
            AdminFixtures::class,
        ];
    }

    private function makeItemBatch(
        $refName,
        $receiptRef,
        $inventoryRef,
        $quantity,
        $adminRef,
        $price = 2000,
        $supplierBarcode = "test"
    ): ItemBatch {
        $itemBatch = new ItemBatch();
        $itemBatch->setReceipt($this->getReference($receiptRef));
        $itemBatch->setInventory($this->getReference($inventoryRef));
        $itemBatch->setConsumerPrice($price);
        $itemBatch->setExpireAt(new \DateTime());
        $itemBatch->setQuantity($quantity);
        $itemBatch->setSupplierBarcode($supplierBarcode);
        $itemBatch->setCreatedBy($this->getReference($adminRef)->getEmail());
        $itemBatch->setUpdatedBy($this->getReference($adminRef)->getEmail());
        $itemBatch->setUpdatedAt(new \DateTime());
        $itemBatch->setCreatedAt(new \DateTime());

        $this->addReference($refName, $itemBatch);

        return $itemBatch;
    }
}
