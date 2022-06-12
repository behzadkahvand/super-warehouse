<?php

namespace App\Repository;

use App\Entity\WarehouseStorageArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WarehouseStorageArea|null find($id, $lockMode = null, $lockVersion = null)
 * @method WarehouseStorageArea|null findOneBy(array $criteria, array $orderBy = null)
 * @method WarehouseStorageArea[]    findAll()
 * @method WarehouseStorageArea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarehouseStorageAreaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarehouseStorageArea::class);
    }

    // /**
    //  * @return WarehouseStorageArea[] Returns an array of WarehouseStorageArea objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WarehouseStorageArea
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
