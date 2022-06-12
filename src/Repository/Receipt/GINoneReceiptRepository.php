<?php

namespace App\Repository\Receipt;

use App\Entity\Receipt\GINoneReceipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GINoneReceipt|null find($id, $lockMode = null, $lockVersion = null)
 * @method GINoneReceipt|null findOneBy(array $criteria, array $orderBy = null)
 * @method GINoneReceipt[]    findAll()
 * @method GINoneReceipt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GINoneReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GINoneReceipt::class);
    }
}
