<?php

namespace App\Repository;

use App\Entity\ItemBatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ItemBatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemBatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemBatch[]    findAll()
 * @method ItemBatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemBatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemBatch::class);
    }

    // /**
    //  * @return ItemBatch[] Returns an array of ItemBatch objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ItemBatch
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
