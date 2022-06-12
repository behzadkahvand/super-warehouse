<?php

namespace App\Repository;

use App\Entity\SellerPackageItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SellerPackageItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method SellerPackageItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method SellerPackageItem[]    findAll()
 * @method SellerPackageItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellerPackageItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SellerPackageItem::class);
    }

    // /**
    //  * @return PackageItem[] Returns an array of PackageItem objects
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
    public function findOneBySomeField($value): ?PackageItem
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
