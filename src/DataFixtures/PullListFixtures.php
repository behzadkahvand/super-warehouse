<?php

namespace App\DataFixtures;

use App\Dictionary\PullListPriorityDictionary;
use App\Dictionary\PullListStatusDictionary;
use App\Entity\PullList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PullListFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist(
            $this->createPullList(
                "pull_list_1",
                "warehouse_1",
                PullListStatusDictionary::DRAFT,
                PullListPriorityDictionary::HIGH
            )
        );
        $manager->persist(
            $this->createPullList(
                "pull_list_2",
                "warehouse_1",
                PullListStatusDictionary::SENT_TO_LOCATOR,
                PullListPriorityDictionary::HIGH
            )->setLocator($this->getReference('admin_1'))
        );
        $manager->persist(
            $this->createPullList(
                "pull_list_3",
                "warehouse_2",
                PullListStatusDictionary::STOWING,
                PullListPriorityDictionary::MEDIUM
            )->setLocator($this->getReference('admin_1'))
        );
        $manager->persist(
            $this->createPullList(
                "pull_list_4",
                "warehouse_2",
                PullListStatusDictionary::DRAFT,
                PullListPriorityDictionary::LOW
            )
        );
        $manager->persist(
            $this->createPullList(
                "pull_list_5",
                "warehouse_1",
                PullListStatusDictionary::STOWING,
                PullListPriorityDictionary::HIGH
            )->setLocator($this->getReference('admin_1'))
        );
        $manager->persist(
            $this->createPullList(
                "pull_list_6",
                "warehouse_1",
                PullListStatusDictionary::CONFIRMED_BY_LOCATOR,
                PullListPriorityDictionary::LOW
            )->setLocator($this->getReference('admin_1'))
        );

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
            AdminFixtures::class,
        ];
    }

    private function createPullList(
        string $reference,
        string $warehouseReference,
        string $status,
        string $priority
    ): PullList {
        $pullList = new PullList();

        $pullList->setWarehouse($this->getReference($warehouseReference))
                 ->setStatus($status)
                 ->setPriority($priority);

        $this->addReference($reference, $pullList);

        return $pullList;
    }
}
