<?php

namespace App\Repository;

use App\Dictionary\PullListSortedPriorityDictionary;
use App\Dictionary\PullListStatusDictionary;
use App\Entity\Admin;
use App\Entity\PullList;
use App\Entity\Receipt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PullList|null find($id, $lockMode = null, $lockVersion = null)
 * @method PullList|null findOneBy(array $criteria, array $orderBy = null)
 * @method PullList[]    findAll()
 * @method PullList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PullListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PullList::class);
    }

    public function getReceiptPullListItemsCount(Receipt $receipt): ?int
    {
        return $this->createQueryBuilder('pullList')
                    ->innerJoin("pullList.items", 'pullListItem')
                    ->where('pullListItem.receipt = :receipt')
                    ->setParameter('receipt', $receipt)
                    ->select("SUM(pullListItem.quantity)")
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function latestActivePullListCount(Admin $locator): int
    {
        return $this->createQueryBuilder('PullList')
                    ->select('COUNT(PullList.id)')
                    ->where('PullList.status IN(:statuses)')
                    ->andWhere('IDENTITY(PullList.locator) = :locator')
                    ->setParameters([
                        'statuses' => [
                            PullListStatusDictionary::CONFIRMED_BY_LOCATOR,
                            PullListStatusDictionary::STOWING,
                        ],
                        'locator'  => $locator,
                    ])
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getLatestLocatorActivePullList(Admin $locator): ?PullList
    {
        $pullList = $this->createQueryBuilder('PullList')
                         ->select('Partial PullList.{id, priority}')
                         ->innerJoin('PullList.items', 'PullListItem')
                         ->addSelect('Partial PullListItem.{id, quantity, remainQuantity}')
                         ->innerJoin('PullListItem.receiptItem', 'ReceiptItem')
                         ->addSelect('Partial ReceiptItem.{id}')
                         ->innerJoin('PullList.warehouse', 'Warehouse')
                         ->addSelect('Partial Warehouse.{id, title}')
                         ->innerJoin('ReceiptItem.inventory', 'Inventory')
                         ->addSelect('Partial Inventory.{id, color, guarantee, size}')
                         ->innerJoin('Inventory.product', 'Product')
                         ->addSelect('Partial Product.{id, title}')
                         ->where('PullList.status IN(:statuses)')
                         ->andWhere('IDENTITY(PullList.locator) = :locator')
                         ->setParameters([
                             'statuses' => [
                                 PullListStatusDictionary::CONFIRMED_BY_LOCATOR,
                                 PullListStatusDictionary::STOWING,
                             ],
                             'locator'  => $locator,
                         ])
                         ->setMaxResults(1)
                         ->getQuery()
                         ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
                         ->getResult();

        return $pullList[0] ?? null;
    }

    public function activePullListToLocate(Admin $locator): ?PullList
    {
        $pullList = $this->createQueryBuilder('PullList')
                         ->select('Partial PullList.{id, priority}')
                         ->innerJoin('PullList.items', 'PullListItem')
                         ->addSelect('Partial PullListItem.{id, quantity, remainQuantity}')
                         ->innerJoin('PullListItem.receiptItem', 'ReceiptItem')
                         ->addSelect('Partial ReceiptItem.{id}')
                         ->innerJoin('PullList.warehouse', 'Warehouse')
                         ->addSelect('Partial Warehouse.{id, title}')
                         ->innerJoin('ReceiptItem.inventory', 'Inventory')
                         ->addSelect('Partial Inventory.{id, color, guarantee, size}')
                         ->innerJoin('Inventory.product', 'Product')
                         ->addSelect('Partial Product.{id, title}')
                         ->where('PullList.status = :status')
                         ->andWhere('IDENTITY(PullList.locator) = :locator')
                         ->setParameters([
                             'status'  => PullListStatusDictionary::SENT_TO_LOCATOR,
                             'locator' => $locator,
                         ])
                         ->orderBy(
                             sprintf(
                                 'FIELD(PullList.priority, %s)',
                                 "'" . implode(
                                     "','",
                                     array_map('strval', PullListSortedPriorityDictionary::toArray())
                                 ) . "'"
                             )
                         )
                         ->addOrderBy('PullList.updatedAt')
                         ->setMaxResults(1)
                         ->getQuery()
                         ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
                         ->getResult();

        return $pullList[0] ?? null;
    }
}
