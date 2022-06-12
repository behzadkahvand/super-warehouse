<?php

namespace App\Repository\Receipt;

use App\Entity\Receipt\STOutboundReceipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method STOutboundReceipt|null find($id, $lockMode = null, $lockVersion = null)
 * @method STOutboundReceipt|null findOneBy(array $criteria, array $orderBy = null)
 * @method STOutboundReceipt[]    findAll()
 * @method STOutboundReceipt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class STOutboundReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, STOutboundReceipt::class);
    }
}
