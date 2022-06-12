<?php

namespace App\DataFixtures;

use App\Entity\WarehouseStock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WarehouseStockFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $warehouseStock = new WarehouseStock();
        $warehouseStock->setWarehouse($this->getReference('warehouse_1'));
        $warehouseStock->setInventory($this->getReference('inventory_1'));
        $warehouseStock->setProduct($this->getReference('product_1'));
        $warehouseStock->setSeller($this->getReference('seller_1'));
        $warehouseStock->setPhysicalStock(12);
        $warehouseStock->setSaleableStock(12);
        $warehouseStock->setReserveStock(0);
        $warehouseStock->setQuarantineStock(0);
        $warehouseStock->setSupplyStock(0);

        $manager->persist($warehouseStock);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
            InventoryFixtures::class,
            ProductFixtures::class,
            SellerFixtures::class,
        ];
    }
}
