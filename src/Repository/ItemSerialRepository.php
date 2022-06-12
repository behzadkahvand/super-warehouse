<?php

namespace App\Repository;

use App\Dictionary\ItemSerialStatusDictionary;
use App\Entity\ItemBatch;
use App\Entity\Inventory;
use App\Entity\ItemSerial;
use App\Entity\Product;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ItemSerial|null find($id, $lockMode = null, $lockVersion = null)
 * @method ItemSerial|null findOneBy(array $criteria, array $orderBy = null)
 * @method ItemSerial[]    findAll()
 * @method ItemSerial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemSerialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemSerial::class);
    }

    public function getItemSerialIdsHasNoSerial(): array
    {
        $result = $this->createQueryBuilder('ItemSerials')
                       ->select('DISTINCT ItemSerials.id')
                       ->where('ItemSerials.serial IS NULL')
                       ->getQuery()
                       ->getResult();

        return array_column($result, 'id');
    }

    public function getItemSerialsWithInventoryQueryBuilder(Inventory $inventory): QueryBuilder
    {
        $qb = $this->createQueryBuilder('itemSerial')
                   ->innerJoin('itemSerial.warehouseStorageBin', 'storageBin');

        return $qb->where('itemSerial.inventory = :inventory')
                  ->andWhere('itemSerial.status = :status')
                  ->andWhere('storageBin.isActiveForPick = :activeForPick')
                  ->setParameters([
                      'inventory'     => $inventory,
                      'status'        => ItemSerialStatusDictionary::SALABLE_STOCK,
                      'activeForPick' => true,
                  ])
                  ->groupBy('itemSerial.inventory')
                  ->addGroupBy('itemSerial.warehouseStorageBin')
                  ->addSelect('count(itemSerial.id) as total');
    }

    public function getItemsCountWithDifferentBatchInSpecificBin(WarehouseStorageBin $storageBin, ItemBatch $batch): ?int
    {
        return $this->createQueryBuilder('itemSerial')
                    ->select('count(itemSerial.id)')
                    ->where('itemSerial.status = :salableStatus')
                    ->andWhere('itemSerial.warehouseStorageBin = :storageBin')
                    ->andWhere('itemSerial.itemBatch != :batch')
                    ->setParameters([
                        'salableStatus' => ItemSerialStatusDictionary::SALABLE_STOCK,
                        'storageBin'    => $storageBin,
                        'batch'         => $batch,
                    ])
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getSameProductItemsCountWithDifferentBatchInSpecificBin(
        WarehouseStorageBin $storageBin,
        ItemBatch $batch,
        Product $product
    ): ?int {
        return $this->createQueryBuilder('itemSerial')
                    ->innerJoin('itemSerial.inventory', 'inventory')
                    ->select('count(itemSerial.id)')
                    ->where('itemSerial.status = :salableStatus')
                    ->andWhere('itemSerial.warehouseStorageBin = :storageBin')
                    ->andWhere('itemSerial.itemBatch != :batch')
                    ->andWhere('inventory.product = :product')
                    ->setParameters([
                        'salableStatus' => ItemSerialStatusDictionary::SALABLE_STOCK,
                        'storageBin'    => $storageBin,
                        'batch'         => $batch,
                        'product'       => $product,
                    ])
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getPullListSerials(PullList $pullList): array
    {
        return $this->createQueryBuilder('itemSerial')
                    ->innerJoin('itemSerial.receiptItemSerials', 'receiptItemSerials')
                    ->innerJoin('receiptItemSerials.receiptItem', 'receiptItem')
                    ->innerJoin('receiptItem.pullListItem', 'pullListItem')
                    ->where('pullListItem.pullList = :pullList')
                    ->setParameter('pullList', $pullList)
                    ->getQuery()
                    ->getResult();
    }

    public function getPullListItemSerials(PullListItem $pullListItem): array
    {
        return $this->createQueryBuilder('itemSerial')
                    ->innerJoin('itemSerial.receiptItemSerials', 'receiptItemSerials')
                    ->innerJoin('receiptItemSerials.receiptItem', 'receiptItem')
                    ->innerJoin('receiptItem.pullListItem', 'pullListItem')
                    ->where('pullListItem.id = :pullListItem')
                    ->setParameter('pullListItem', $pullListItem)
                    ->getQuery()
                    ->getResult();
    }

    public function getStorageBinItemSerialsQuantity(WarehouseStorageBin $storageBin): ?int
    {
        return $this->createQueryBuilder('itemSerial')
                    ->select('count(itemSerial.id)')
                    ->where('itemSerial.status = :salableStatus')
                    ->andWhere('itemSerial.warehouseStorageBin = :storageBin')
                    ->setParameters([
                        'salableStatus' => ItemSerialStatusDictionary::SALABLE_STOCK,
                        'storageBin'    => $storageBin,
                    ])
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getItemSerialsCountByInventoryInSpecificBin(
        Inventory $inventory,
        WarehouseStorageBin $storageBin
    ): ?int {
        return $this->createQueryBuilder('itemSerial')
                    ->select('count(itemSerial.id)')
                    ->where('itemSerial.status = :salableStatus')
                    ->andWhere('itemSerial.warehouseStorageBin = :storageBin')
                    ->andWhere('itemSerial.inventory = :inventory')
                    ->setParameters([
                        'salableStatus' => ItemSerialStatusDictionary::SALABLE_STOCK,
                        'storageBin'    => $storageBin,
                        'inventory'     => $inventory,
                    ])
                    ->getQuery()
                    ->getSingleScalarResult();
    }
}
