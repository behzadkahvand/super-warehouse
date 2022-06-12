<?php

namespace App\Repository\Receipt;

use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GRMarketPlacePackageReceipt|null find($id, $lockMode = null, $lockVersion = null)
 * @method GRMarketPlacePackageReceipt|null findOneBy(array $criteria, array $orderBy = null)
 * @method GRMarketPlacePackageReceipt[]    findAll()
 * @method GRMarketPlacePackageReceipt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GRMarketPlacePackageReceiptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GRMarketPlacePackageReceipt::class);
    }
}
