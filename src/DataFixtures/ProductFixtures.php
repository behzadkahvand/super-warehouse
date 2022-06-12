<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setId(1);
        $product->setTitle('test');
        $product->setHeight(200);
        $product->setWidth(200);
        $product->setLength(200);
        $product->setWeight(100);
        $product->setMainImage('image.jpeg');

        $this->addReference('product_1', $product);

        $manager->persist($product);
        $manager->flush();
    }
}
