<?php

namespace App\Repository;

use App\Entity\ReceiptItemBatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReceiptItemBatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptItemBatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptItemBatch[]    findAll()
 * @method ReceiptItemBatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptItemBatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptItemBatch::class);
    }

    // /**
    //  * @return ReceiptItemBatch[] Returns an array of ReceiptItemBatch objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReceiptItemBatch
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
