<?php

namespace App\DataFixtures;

use App\Dictionary\SellerPackageStatusDictionary;
use App\Dictionary\SellerPackageProductTypeDictionary;
use App\Dictionary\SellerPackageTypeDictionary;
use App\Entity\SellerPackage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SellerPackageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sentPackage = $this->createSellerPackage(
            1,
            SellerPackageStatusDictionary::SENT,
            'sentPackage'
        );
        $manager->persist($sentPackage);

        $receivedPackage = $this->createSellerPackage(
            2,
            SellerPackageStatusDictionary::RECEIVED,
            'receivedPackage'
        );
        $manager->persist($receivedPackage);

        $canceledPackage = $this->createSellerPackage(
            3,
            SellerPackageStatusDictionary::CANCELED,
            'canceledPackage'
        );
        $manager->persist($canceledPackage);

        $partialReceivedPackage = $this->createSellerPackage(
            4,
            SellerPackageStatusDictionary::PARTIAL_RECEIVED,
            'partialReceivedPackage'
        );
        $manager->persist($partialReceivedPackage);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SellerFixtures::class,
        ];
    }

    private function createSellerPackage(int $id, string $status, string $referenceName): SellerPackage
    {
        $package = new SellerPackage();
        $package->setId($id);
        $package->setStatus($status);
        $package->setPackageType(SellerPackageTypeDictionary::DEPOT);
        $package->setProductType(SellerPackageProductTypeDictionary::NON_FMCG);
        $package->setSeller($this->getReference('seller_1'));
        $package->setWarehouse($this->getReference('warehouse_1'));
        $package->setCreatedAt(new \DateTime());

        $this->addReference($referenceName, $package);

        return $package;
    }
}
