<?php

namespace App\Repository;

use App\Entity\ItemSerial;
use App\Entity\PullList;
use App\Entity\PullListItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PullListItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PullListItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PullListItem[]    findAll()
 * @method PullListItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PullListItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PullListItem::class);
    }

    public function findPullListItemByPullListAndItemSerial(PullList $pullList, ItemSerial $itemSerial): ?PullListItem
    {
        $pullListItem = $this->createQueryBuilder('pullListItem')
                             ->innerJoin("pullListItem.receiptItem", 'receiptItem')
                             ->innerJoin("receiptItem.receiptItemSerials", 'receiptItemSerial')
                             ->where('receiptItemSerial.itemSerial = :itemSerial')
                             ->andWhere('pullListItem.pullList = :pullList')
                             ->setParameters([
                                 'itemSerial' => $itemSerial,
                                 'pullList'   => $pullList,
                             ])
                             ->setMaxResults(1)
                             ->getQuery()
                             ->getResult();

        return $pullListItem[0] ?? null;
    }

    public function getItemsByPullList(PullList $pullList): array
    {
        return $this->createQueryBuilder('PullListItem')
                    ->select('Partial PullListItem.{id, quantity, remainQuantity, status}')
                    ->innerJoin("PullListItem.receiptItem", 'ReceiptItem')
                    ->addSelect('Partial ReceiptItem.{id}')
                    ->innerJoin("PullListItem.receipt", 'Receipt')
                    ->addSelect('Partial Receipt.{id}')
                    ->where('IDENTITY(PullListItem.pullList) = :pullList')
                    ->setParameters([
                        'pullList' => $pullList,
                    ])
                    ->getQuery()
                    ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
                    ->getResult();
    }
}
