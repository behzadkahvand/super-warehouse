<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(protected UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Admin();
        $admin->setName('admin');
        $admin->setFamily('admin');
        $admin->setEmail('admin@warehouse.com');
        $admin->setIsActive(1);
        $admin->setMobile('09121234567');
        $admin->setPassword($this->hasher->hashPassword($admin, '123456'));

        $this->addReference('admin_1', $admin);

        $manager->persist($admin);
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [
            'admin'
        ];
    }
}
