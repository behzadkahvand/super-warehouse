<?php

namespace App\DataFixtures;

use App\Dictionary\SellerPackageStatusDictionary;
use App\Entity\Seller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SellerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $seller = new Seller();
        $seller->setId(1);
        $seller->setName('test');
        $seller->setIdentifier(random_int(10000000, 20000000));
        $seller->setMobile('09121234567');

        $this->addReference('seller_1', $seller);

        $manager->persist($seller);
        $manager->flush();
    }
}
