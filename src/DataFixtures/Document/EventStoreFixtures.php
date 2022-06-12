<?php

namespace App\DataFixtures\Document;

use App\Document\Integration\Timcheh\EventStore;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventStoreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $event = new EventStore();
        $event->setSourceServiceName('test')
            ->setMessageId('test')
            ->setMessageName('test')
            ->setPayload(['title' => 'test'])
            ->setCreatedAt(new \DateTime());

        $manager->persist($event);
        $manager->flush();
    }
}
