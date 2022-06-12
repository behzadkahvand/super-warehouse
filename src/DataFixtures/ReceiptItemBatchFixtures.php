<?php

namespace App\DataFixtures;

use App\Entity\ReceiptItemBatch;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReceiptItemBatchFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->create('receipt_item_batch_1', 'receipt_item_1', 'item_batch_1'));
        $manager->persist($this->create('receipt_item_batch_2', 'receipt_item_52', 'item_batch_4'));
        $manager->persist($this->create('receipt_item_batch_3', 'receipt_item_54', 'item_batch_6'));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ReceiptItemFixtures::class,
            ItemBatchFixtures::class,
            AdminFixtures::class,
        ];
    }

    private function create(string $refName, string $receiptItemRef, string $itemBatchRef): ReceiptItemBatch
    {
        $receiptItemBatch = new ReceiptItemBatch();
        $receiptItemBatch->setReceiptItem($this->getReference($receiptItemRef));
        $receiptItemBatch->setItemBatch($this->getReference($itemBatchRef));
        $receiptItemBatch->setCreatedBy($this->getReference('admin_1')->getEmail());
        $receiptItemBatch->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $receiptItemBatch->setUpdatedAt(new \DateTime());
        $receiptItemBatch->setCreatedAt(new \DateTime());

        $this->addReference($refName, $receiptItemBatch);

        return $receiptItemBatch;
    }
}
