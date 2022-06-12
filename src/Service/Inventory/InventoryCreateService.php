<?php

namespace App\Service\Inventory;

use App\Repository\ProductRepository;
use App\Service\Inventory\DTO\InventoryData;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class InventoryCreateService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ProductRepository $productRepository,
        private InventoryFactory $factory
    ) {
    }

    public function create(InventoryData $data): void
    {
        $product = $this->productRepository->find($data->getProductId());

        if (!$product) {
            throw new EntityNotFoundException('product of inventory not found!');
        }

        $inventory = $this->factory->create()
                                   ->setId($data->getId())
                                   ->setGuarantee($data->getGuarantee())
                                   ->setSize($data->getSize())
                                   ->setColor($data->getColor())
                                   ->setProduct($product);

        $this->manager->persist($inventory);
        $this->manager->flush();
    }
}
