<?php

namespace App\Repository;

use App\Entity\ReceiptItem;
use App\Entity\ShipmentItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShipmentItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentItem[]    findAll()
 * @method ShipmentItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentItem::class);
    }

    public function getPartialShipmentItemByReceiptItem(ReceiptItem $receiptItem): ?ShipmentItem
    {
        return $this->createQueryBuilder('shipmentItem')
            ->where('shipmentItem.receiptItem = :receiptItem')
            ->setParameter('receiptItem', $receiptItem)
            ->select('PARTIAL shipmentItem.{id}')
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getOneOrNullResult();
    }
}
