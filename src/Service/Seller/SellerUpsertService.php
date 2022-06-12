<?php

namespace App\Service\Seller;

use App\Repository\SellerRepository;
use App\Service\Seller\DTO\SellerData;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class SellerUpsertService
{
    public function __construct(
        private EntityManagerInterface $manager,
        private SellerRepository $repository,
        private SellerFactory $factory
    ) {
    }

    public function create(SellerData $data): void
    {
        $seller = $this->factory->create()
                                ->setId($data->getId())
                                ->setIdentifier($data->getIdentifier())
                                ->setName($data->getName())
                                ->setMobile($data->getMobile());

        $this->manager->persist($seller);
        $this->manager->flush();
    }

    public function update(SellerData $data): void
    {
        $seller = $this->repository->find($data->getId());

        if (!$seller) {
            throw new EntityNotFoundException('Seller not found!');
        }

        $seller->setIdentifier($data->getIdentifier())
               ->setName($data->getName())
               ->setMobile($data->getMobile());

        $this->manager->flush();
    }
}
