<?php

namespace App\DataFixtures;

use App\Dictionary\SellerPackageItemStatusDictionary;
use App\Entity\SellerPackageItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SellerPackageItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $j = 1;
        for ($i = 1; $i <= 2; $i++) {
            $sentPackageItem = $this->createPackageItem(
                $i,
                'inventory_' . $j,
                'sentPackage',
                SellerPackageItemStatusDictionary::SENT,
                10
            );
            $manager->persist($sentPackageItem);
            $j++;
        }

        $j = 1;
        for ($i = 3; $i <= 4; $i++) {
            $receivedPackageItem = $this->createPackageItem(
                $i,
                'inventory_' . $j,
                'receivedPackage',
                SellerPackageItemStatusDictionary::RECEIVED,
                10
            );

            $manager->persist($receivedPackageItem);
            $j++;
        }

        $canceledPackageItem = $this->createPackageItem(
            5,
            'inventory_1',
            'canceledPackage',
            SellerPackageItemStatusDictionary::CANCELED,
            10
        );

        $manager->persist($canceledPackageItem);

        $partialReceivedPackageItem1 = $this->createPackageItem(
            6,
            'inventory_1',
            'partialReceivedPackage',
            SellerPackageItemStatusDictionary::PARTIAL_RECEIVED,
            10
        );

        $manager->persist($partialReceivedPackageItem1);

        $partialReceivedPackageItem2 = $this->createPackageItem(
            7,
            'inventory_2',
            'partialReceivedPackage',
            SellerPackageItemStatusDictionary::RECEIVED,
            10
        );

        $manager->persist($partialReceivedPackageItem2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SellerPackageFixtures::class,
            InventoryFixtures::class,
        ];
    }

    private function createPackageItem(
        int $id,
        string $inventoryName,
        string $packageName,
        string $status,
        int $expectedQuantity,
        int $actualQuantity = null
    ): SellerPackageItem {
        $packageItem = new SellerPackageItem();
        $packageItem->setId($id);
        $packageItem->setStatus($status);
        $packageItem->setExpectedQuantity($expectedQuantity);
        $packageItem->setActualQuantity($actualQuantity ?? $expectedQuantity);
        $packageItem->setInventory($this->getReference($inventoryName));
        $packageItem->setSellerPackage($this->getReference($packageName));

        return $packageItem;
    }
}
