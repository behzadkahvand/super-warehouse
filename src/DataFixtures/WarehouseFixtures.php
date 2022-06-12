<?php

namespace App\DataFixtures;

use App\Dictionary\WarehousePickingStrategyDictionary;
use App\Dictionary\WarehousePickingTypeDictionary;
use App\Dictionary\WarehouseTrackingTypeDictionary;
use App\Entity\Warehouse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;

class WarehouseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->makeWarehouse("warehouse_1", "test1"));
        $manager->persist($this->makeWarehouse("warehouse_2", "test2"));
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AdminFixtures::class,
        ];
    }

    private function makeWarehouse(
        string $reference,
        string $title,
        string $address = "test",
        int $isMArketPlacePurchase = 1,
        int $isFmcgMArketPlacePurchase = 1,
        int $isForSale = 0,
        int $isForSaleReturn = 0,
        int $isForRetailPurchase = 0,
        int $isActive = 1
    ): Warehouse {
        $warehouse = new Warehouse();
        $warehouse->setIsActive($isActive);
        $warehouse->setAddress($address);
        $warehouse->setForMarketPlacePurchase($isMArketPlacePurchase);
        $warehouse->setForFmcgMarketPlacePurchase($isFmcgMArketPlacePurchase);
        $warehouse->setForSale($isForSale);
        $warehouse->setForSalesReturn($isForSaleReturn);
        $warehouse->setForRetailPurchase($isForRetailPurchase);
        $warehouse->setPhone('09121234567');
        $warehouse->setTitle($title);
        $warehouse->setTrackingType(WarehouseTrackingTypeDictionary::BATCH);
        $warehouse->setPickingStrategy(WarehousePickingStrategyDictionary::FIFO);
        $warehouse->setPickingType(WarehousePickingTypeDictionary::SHIPMENT);
        $warehouse->setCoordinates(new Point(35.3245, 51.2345));
        $warehouse->setOwner($this->getReference('admin_1'));
        $warehouse->setCreatedBy($this->getReference('admin_1')->getEmail());
        $warehouse->setUpdatedBy($this->getReference('admin_1')->getEmail());
        $warehouse->setUpdatedAt(new \DateTime());
        $warehouse->setCreatedAt(new \DateTime());

        $this->addReference($reference, $warehouse);

        return $warehouse;
    }
}
