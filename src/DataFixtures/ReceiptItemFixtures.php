<?php

namespace App\DataFixtures;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ReceiptItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReceiptItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createReceiptItem(
            $this->getReference('receipt_1'),
            10,
            'receipt_item_1'
        ));
        $manager->persist($this->createReceiptItem(
            $this->getReference('receipt_1'),
            5,
            'receipt_item_2'
        ));
        $manager->persist($this->createReceiptItem(
            $this->getReference('receipt_1'),
            8,
            'receipt_item_3',
            ReceiptStatusDictionary::DRAFT
        ));

        $manager->persist($this->createReceiptItem(
            $this->getReference('receipt_4'),
            15,
            'receipt_item_4',
            ReceiptStatusDictionary::DRAFT
        ));

        $receiptItem5 = $this->createReceiptItem(
            $this->getReference('receipt_with_shipment_reference'),
            1,
            'receipt_item_5'
        );

        $manager->persist($receiptItem5);

        $lastReceiptItemId = 5;
        for ($i = 1; $i <= 20; $i++) {
            $manager->persist($this->createReceiptItem(
                $this->getReference('receipt_package_' . $i),
                15,
                'receipt_item_' . $i + $lastReceiptItemId,
                ReceiptStatusDictionary::READY_TO_STOW,
                'inventory_' . rand(1, 3)
            ));

            $lastReceiptItemId++;

            $manager->persist($this->createReceiptItem(
                $this->getReference('receipt_package_' . $i),
                20,
                'receipt_item_' . $i + $lastReceiptItemId,
                ReceiptStatusDictionary::READY_TO_STOW,
                'inventory_' . rand(1, 3)
            ));
        }
        $manager->persist($this->createReceiptItem(
            $this->getReference('receipt_6'),
            1,
            'receipt_item_50',
            ReceiptStatusDictionary::READY_TO_PICK
        ));
        $manager->persist($this->createReceiptItem(
            $this->getReference('receipt_7'),
            1,
            'receipt_item_51',
            ReceiptStatusDictionary::PICKING
        ));
        $manager->persist($this->createReceiptItem(
            $this->getReference('receipt_8'),
            2,
            'receipt_item_52',
            ReceiptStatusDictionary::STOWING
        ));
        $manager->persist($this->createReceiptItem(
            $this->getReference('receipt_8'),
            1,
            'receipt_item_53',
            ReceiptStatusDictionary::STOWING
        ));
        $manager->persist($this->createReceiptItem(
            $this->getReference('receipt_10'),
            1,
            'receipt_item_54',
            ReceiptStatusDictionary::DONE
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

    private function createReceiptItem(
        object $receiptReference,
        int $quantity,
        string $referenceName,
        string $status = ReceiptStatusDictionary::APPROVED,
        string $inventoryReferenceName = 'inventory_1'
    ): ReceiptItem {
        $receiptItem = new ReceiptItem();
        $receiptItem->setReceipt($receiptReference);
        $receiptItem->setInventory($this->getReference($inventoryReferenceName));
        $receiptItem->setStatus($status);
        $receiptItem->setQuantity($quantity);
        $receiptItem->setCreatedBy($this->getReference('admin_1')->getEmail());
        $receiptItem->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $receiptItem->setUpdatedAt(new \DateTime());
        $receiptItem->setCreatedAt(new \DateTime());

        $this->addReference($referenceName, $receiptItem);

        return $receiptItem;
    }
}
