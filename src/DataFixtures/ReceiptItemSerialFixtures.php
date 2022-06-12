<?php

namespace App\DataFixtures;

use App\Entity\ReceiptItemSerial;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReceiptItemSerialFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->create('receipt_item_serial_1', 'receipt_item_1', 'item_serial_1'));
        $manager->persist($this->create('receipt_item_serial_2', 'receipt_item_52', 'item_serial_4'));
        $manager->persist($this->create('receipt_item_serial_3', 'receipt_item_52', 'item_serial_5'));
        $manager->persist($this->create('receipt_item_serial_4', 'receipt_item_53', 'item_serial_6'));
        $manager->persist($this->create('receipt_item_serial_5', 'receipt_item_54', 'item_serial_9'));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ReceiptItemFixtures::class,
            ItemSerialFixtures::class,
            AdminFixtures::class,
        ];
    }

    private function create(
        $refName,
        $receiptItemRef,
        $itemSerialRef,
    ): ReceiptItemSerial {
        $receiptItemSerial = new ReceiptItemSerial();
        $receiptItemSerial->setReceiptItem($this->getReference($receiptItemRef));
        $receiptItemSerial->setItemSerial($this->getReference($itemSerialRef));
        $receiptItemSerial->setCreatedBy($this->getReference('admin_1')->getEmail());
        $receiptItemSerial->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $receiptItemSerial->setUpdatedAt(new \DateTime());
        $receiptItemSerial->setCreatedAt(new \DateTime());

        $this->addReference($refName, $receiptItemSerial);

        return $receiptItemSerial;
    }
}
