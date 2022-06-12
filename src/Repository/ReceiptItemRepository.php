<?php

namespace App\Repository;

use App\Entity\ReceiptItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReceiptItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceiptItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceiptItem[]    findAll()
 * @method ReceiptItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceiptItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceiptItem::class);
    }

    // /**
    //  * @return ReceiptItem[] Returns an array of ReceiptItem objects
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
    public function findOneBySomeField($value): ?ReceiptItem
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getTotalReceiptItemBatchQuantities(ReceiptItem $receiptItem)
    {
        return $this->createQueryBuilder('receiptItem')
                       ->join('receiptItem.receiptItemBatches', 'receiptItemBatch')
                       ->join('receiptItemBatch.itemBatch', 'itemBatch')
                       ->select('sum(itemBatch.quantity) as total')
                       ->where('receiptItem.id = :receiptItemId')
                       ->setParameter('receiptItemId', $receiptItem->getId())
                       ->getQuery()
                       ->getSingleScalarResult();
    }
}
