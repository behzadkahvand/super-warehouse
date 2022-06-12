<?php

namespace App\Repository\Receipt;

use App\Entity\Receipt\STInboundReceipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method STInboundReceipt|null find($id, $lockMode = null, $lockVersion = null)
 * @method STInboundReceipt|null findOneBy(array $criteria, array $orderBy = null)
 * @method STInboundReceipt[]    findAll()
 * @method STInboundReceipt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class STInboundReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, STInboundReceipt::class);
    }
}
