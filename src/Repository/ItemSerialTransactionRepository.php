<?php

namespace App\Repository;

use App\Document\ItemSerialTransaction;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

/**
 * @method ItemSerialTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemSerialTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemSerialTransaction[]    findAll()
 * @method ItemSerialTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemSerialTransactionRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemSerialTransaction::class);
    }
}
