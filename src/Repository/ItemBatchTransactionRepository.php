<?php

namespace App\Repository;

use App\Dictionary\ItemTransactionActionTypeDictionary;
use App\Document\ItemBatchTransaction;
use DateTime;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;

/**
 * @method ItemBatchTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemBatchTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemBatchTransaction[]    findAll()
 * @method ItemBatchTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemBatchTransactionRepository extends ServiceDocumentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemBatchTransaction::class);
    }

    public function findItemBatchRelocated(int $locatorId, int $storageBinId, int $batchId): ?object
    {
        return $this->createQueryBuilder()
            ->field('actionType')->equals(ItemTransactionActionTypeDictionary::RELOCATE)
            ->field('warehouseStorageBinId')->equals($storageBinId)
            ->field('itemBatchId')->equals($batchId)
            ->field('updatedBy')->equals($locatorId)
            ->field('createdAt')->gte(new DateTime("-7 days"))
            ->getQuery()
            ->getSingleResult();
    }
}
