<?php

namespace App\Service\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Product\DTO\ProductData;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class ProductService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ProductRepository $repository,
        private ProductFactory $factory
    ) {
    }

    public function create(ProductData $data): void
    {
        $product = $this->factory->create();

        $product->setId($data->getId());
        $product->setTitle($data->getTitle());
        $product->setMainImage($data->getMainImage());
        $product->setLength($data->getLength());
        $product->setWidth($data->getWidth());
        $product->setHeight($data->getHeight());
        $product->setWeight($data->getWeight());

        $this->manager->persist($product);
        $this->manager->flush();
    }

    public function update(ProductData $data): void
    {
        /** @var Product $product */
        $product = $this->repository->find($data->getId());
        if (!$product) {
            throw new EntityNotFoundException('Product not found!');
        }

        $product->setTitle($data->getTitle());
        $product->setMainImage($data->getMainImage());
        $product->setLength($data->getLength());
        $product->setWidth($data->getWidth());
        $product->setHeight($data->getHeight());
        $product->setWeight($data->getWeight());

        $this->manager->flush();
    }
}
