<?php

namespace App\DataFixtures\Document;

use App\Document\Integration\Timcheh\LogStore;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LogStoreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $logStore = new LogStore();
        $logStore
            ->setMessageId('test')
            ->setStatus('PROCESSING')
            ->setCreatedAt(new \DateTime());

        $manager->persist($logStore);
        $manager->flush();
    }
}
