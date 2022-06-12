<?php

namespace App\Repository;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Shipment;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shipment[]    findAll()
 * @method Shipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shipment::class);
    }

    public function getSpecificQuantityOfShipmentsWithReadyToPickReceiptWithinDeliveryRange(
        DateTimeInterface $from,
        DateTimeInterface $to,
        int $count
    ) {
        return $this->createQueryBuilder('shipment')
                    ->innerJoin('shipment.receipt', 'receipt')
                    ->innerJoin('shipment.shipmentItems', 'shipmentItem')
                    ->where('receipt.status = :receiptStatus')
                    ->andWhere('shipment.deliveryDate BETWEEN :from AND :to')
                    ->setParameters([
                        'receiptStatus' => ReceiptStatusDictionary::APPROVED,
                        'from'          => $from->format('Y-m-d'),
                        'to'            => $to->format('Y-m-d'),
                    ])
                    ->setMaxResults($count)
                    ->getQuery()
                    ->getResult();
    }
}
