<?php

namespace App\DataFixtures;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\PullListItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PullListItemFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist(
            $this->createPullListItem(
                "pull_list_item_1",
                "pull_list_4",
                "receipt_1",
                "receipt_item_1",
                10,
                10,
                PullListStatusDictionary::DRAFT
            )
        );
        $manager->persist(
            $this->createPullListItem(
                "pull_list_item_2",
                "pull_list_5",
                "receipt_8",
                "receipt_item_52",
                2,
                1,
                PullListStatusDictionary::STOWING
            )
        );
        $manager->persist(
            $this->createPullListItem(
                "pull_list_item_3",
                "pull_list_6",
                "receipt_8",
                "receipt_item_53",
                1,
                1,
                PullListStatusDictionary::STOWING
            )
        );
        $manager->persist(
            $this->createPullListItem(
                "pull_list_item_4",
                "pull_list_2",
                "receipt_package_2",
                "receipt_item_6",
                15,
                15,
                PullListStatusDictionary::SENT_TO_LOCATOR
            )
        );
        $manager->persist(
            $this->createPullListItem(
                "pull_list_item_5",
                "pull_list_2",
                "receipt_package_2",
                "receipt_item_7",
                20,
                20,
                PullListStatusDictionary::SENT_TO_LOCATOR
            )
        );

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ReceiptFixtures::class,
            ReceiptItemFixtures::class,
            PullListFixtures::class
        ];
    }

    private function createPullListItem(
        string $reference,
        string $pullListReference,
        string $receiptReference,
        string $receiptItemReference,
        int $quantity,
        int $reminingQuantity,
        string $status
    ): PullListItem {
        $pullListItem = new PullListItem();

        $pullListItem->setPullList($this->getReference($pullListReference))
                     ->setReceipt($this->getReference($receiptReference))
                     ->setReceiptItem($this->getReference($receiptItemReference))
                     ->setQuantity($quantity)
                     ->setRemainQuantity($reminingQuantity)
                     ->setStatus($status);

        $this->addReference($reference, $pullListItem);

        return $pullListItem;
    }
}
