<?php

namespace App\Repository;

use App\Entity\SellerPackage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SellerPackage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SellerPackage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SellerPackage[]    findAll()
 * @method SellerPackage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellerPackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SellerPackage::class);
    }

    // /**
    //  * @return Package[] Returns an array of Package objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Package
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
