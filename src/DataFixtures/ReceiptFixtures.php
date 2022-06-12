<?php

namespace App\DataFixtures;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Receipt;
use App\Entity\Receipt\GINoneReceipt;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\Receipt\STInboundReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReceiptFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $receiptWithSellerPackageReference = $this->createGRMarketPlacePackageReceipt(
            'warehouse_1',
            'receipt_package_1',
            ReceiptStatusDictionary::APPROVED
        );

        $manager->persist($receiptWithSellerPackageReference);

        $manager->persist($this->createReceiptWithShipmentReference());

        $receiptWithReceiptReference = $this->createStInboundReceipt($receiptWithSellerPackageReference);
        $manager->persist($receiptWithReceiptReference);

        for ($i = 2; $i <= 10; $i++) {
            $manager->persist($this->createGRMarketPlacePackageReceipt(
                'warehouse_2',
                'receipt_package_' . $i,
                ReceiptStatusDictionary::READY_TO_STOW
            ));
        }
        for ($i = 11; $i <= 20; $i++) {
            $manager->persist($this->createGRMarketPlacePackageReceipt(
                'warehouse_1',
                'receipt_package_' . $i,
                ReceiptStatusDictionary::READY_TO_STOW
            ));
        }

        $manager->persist($this->createGINoneReceipt(
            'receipt_with_none_reference',
            ReceiptStatusDictionary::APPROVED,
            'test3'
        ));
        $manager->persist($this->createSTOutboundReceipt(
            'receipt_4',
            ReceiptStatusDictionary::DRAFT,
            'test4'
        ));
        $manager->persist($this->createSTOutboundReceipt(
            'receipt_5',
            ReceiptStatusDictionary::DRAFT,
            'test5'
        ));
        $manager->persist($this->createSTOutboundReceipt(
            'receipt_6',
            ReceiptStatusDictionary::READY_TO_PICK,
            'test6'
        ));
        $manager->persist($this->createSTOutboundReceipt(
            'receipt_7',
            ReceiptStatusDictionary::PICKING,
            'test7'
        ));
        $manager->persist($this->createGRMarketPlacePackageReceipt(
            'warehouse_1',
            'receipt_8',
            ReceiptStatusDictionary::STOWING,
            'test8'
        ));
        $manager->persist($this->createGRMarketPlacePackageReceipt(
            'warehouse_1',
            'receipt_9',
            ReceiptStatusDictionary::APPROVED,
            'test9'
        ));
        $manager->persist($this->createSTOutboundReceipt(
            'receipt_10',
            ReceiptStatusDictionary::DONE,
            'test10'
        ));

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
            AdminFixtures::class,
            SellerPackageFixtures::class,
            ShipmentFixtures::class,
            SellerPackageFixtures::class,
        ];
    }

    private function createStInboundReceipt(Receipt $receiptReference): STInboundReceipt
    {
        $receipt = new STInboundReceipt();
        $receipt->setSourceWarehouse($this->getReference('warehouse_1'));
        $receipt->setDestinationWarehouse($this->getReference('warehouse_2'));
        $receipt->setStatus(ReceiptStatusDictionary::APPROVED);
        $receipt->setDescription('test1');
        $receipt->setCreatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedAt(new DateTime());
        $receipt->setCreatedAt(new DateTime());
        $receipt->setReference($receiptReference);

        $this->addReference('receipt_1', $receipt);

        return $receipt;
    }

    private function createGRMarketPlacePackageReceipt(
        string $warehouseReferenceName,
        string $referenceName,
        string $status,
        string $description = 'test2'
    ): GRMarketPlacePackageReceipt {
        $receipt = new GRMarketPlacePackageReceipt();
        $receipt->setSourceWarehouse($this->getReference($warehouseReferenceName));
        $receipt->setStatus($status);
        $receipt->setDescription($description);
        $receipt->setCreatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedAt(new DateTime());
        $receipt->setCreatedAt(new DateTime());
        $receipt->setReference($this->getReference('receivedPackage'));

        $this->addReference($referenceName, $receipt);

        return $receipt;
    }

    private function createGINoneReceipt(
        string $referenceName,
        string $status,
        string $description
    ): GINoneReceipt {
        $receipt = new GINoneReceipt();
        $receipt->setSourceWarehouse($this->getReference('warehouse_1'));
        $receipt->setStatus($status);
        $receipt->setDescription($description);
        $receipt->setCreatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedAt(new DateTime());
        $receipt->setCreatedAt(new DateTime());
        $this->addReference($referenceName, $receipt);

        return $receipt;
    }

    private function createSTOutboundReceipt(
        string $referenceName,
        string $status,
        string $description
    ): STOutboundReceipt {
        $receipt = new STOutboundReceipt();
        $receipt->setSourceWarehouse($this->getReference('warehouse_1'));
        $receipt->setStatus($status);
        $receipt->setDescription($description);
        $receipt->setCreatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedAt(new DateTime());
        $receipt->setCreatedAt(new DateTime());
        $this->addReference($referenceName, $receipt);

        return $receipt;
    }

    private function createReceiptWithShipmentReference(): GIShipmentReceipt
    {
        $receipt = new GIShipmentReceipt();
        $receipt->setSourceWarehouse($this->getReference('warehouse_1'));
        $receipt->setStatus(ReceiptStatusDictionary::APPROVED);
        $receipt->setDescription('test2');
        $receipt->setCreatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $receipt->setUpdatedAt(new DateTime());
        $receipt->setCreatedAt(new DateTime());
        $receipt->setReference($this->getReference('shipment_1'));

        $this->addReference('receipt_with_shipment_reference', $receipt);

        return $receipt;
    }
}
