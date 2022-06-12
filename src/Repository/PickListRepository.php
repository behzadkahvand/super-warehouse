<?php

namespace App\Repository;

use App\Dictionary\PickListPriorityDictionary;
use App\Dictionary\PickListStatusDictionary;
use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\Admin;
use App\Entity\Inventory;
use App\Entity\PickList;
use App\Entity\Receipt;
use App\Entity\WarehouseStorageBin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PickList|null find($id, $lockMode = null, $lockVersion = null)
 * @method PickList|null findOneBy(array $criteria, array $orderBy = null)
 * @method PickList[]    findAll()
 * @method PickList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PickListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PickList::class);
    }

    public function getHandHeldPickList(string $receiptType): array
    {
        $pickedReceipt = $this->getHandHeldHighestReceipt($receiptType);

        if (!$pickedReceipt) {
            return [];
        }

        $queryBuilder = $this->makeHandHeldFinalQuery($pickedReceipt[0]['receiptId']);

        return $this->optimizeHandHeldResult($queryBuilder);
    }

    public function getPickerAllActivePickList(Admin $picker): array
    {
        $queryBuilder = $this->createQueryBuilder('pickList')
                             ->innerJoin("pickList.receiptItem", "receiptItem")
                             ->innerJoin("receiptItem.receipt", "receipt")
                             ->leftJoin("receiptItem.receiptItemSerials", "receiptItemSerials")
                             ->innerJoin("pickList.storageBin", "storageBin")
                             ->innerJoin("receiptItem.inventory", "inventory")
                             ->innerJoin("inventory.product", "product")
                             ->where('pickList.picker = :picker')
                             ->andWhere('receipt.status = :receiptStatus')
                             ->setParameters([
                                 "picker"        => $picker,
                                 'receiptStatus' => ReceiptStatusDictionary::PICKING,
                             ]);

        return $this->optimizeHandHeldResult($queryBuilder);
    }

    private function getHandHeldHighestReceipt(string $receiptType): array
    {
        $sortedPriorities = [
            PickListPriorityDictionary::HIGH,
            PickListPriorityDictionary::MEDIUM,
            PickListPriorityDictionary::LOW,
        ];

        return $this->createQueryBuilder('pickList')
                    ->select("receipt.id AS receiptId")
                    ->innerJoin("pickList.receiptItem", 'receiptItem')
                    ->innerJoin("receiptItem.receipt", 'receipt')
                    ->leftJoin("pickList.shipment", 'shipment')
                    ->where("receipt.type = :type")
                    ->andWhere("receipt.status = :receiptStatus")
                    ->groupBy("receipt")
                    ->orderBy(sprintf(
                        'FIELD(%s.priority, \'%s\')',
                        "pickList",
                        implode("', '", $sortedPriorities)
                    ))
                    ->addOrderBy("shipment.deliveryDate")
                    ->addOrderBy("pickList.createdAt")
                    ->setMaxResults(1)
                    ->setParameters([
                        "type"          => $receiptType,
                        "receiptStatus" => ReceiptStatusDictionary::READY_TO_PICK,
                    ])
                    ->getQuery()
                    ->getResult();
    }

    private function makeHandHeldFinalQuery($receiptId): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('pickList');

        $queryBuilder->innerJoin("pickList.receiptItem", "receiptItem")
                     ->leftJoin("receiptItem.receiptItemSerials", "receiptItemSerials")
                     ->innerJoin("pickList.storageBin", "storageBin")
                     ->innerJoin("receiptItem.inventory", "inventory")
                     ->innerJoin("inventory.product", "product")
                     ->where($queryBuilder->expr()
                                          ->in("receiptItem.receipt", $receiptId))
                     ->andWhere("pickList.status = :pickListStatus")
                     ->andWhere("pickList.picker IS NULL")
                     ->setParameters([
                         "pickListStatus" => PickListStatusDictionary::WAITING_FOR_ACCEPT,
                     ]);

        return $queryBuilder;
    }

    private function optimizeHandHeldResult(QueryBuilder $queryBuilder): array
    {
        return $queryBuilder
            ->select("Partial pickList.{id,status,priority,quantity}")
            ->addSelect("Partial storageBin.{id,serial}")
            ->addSelect("Partial receiptItem.{id}")
            ->addSelect("Partial receiptItemSerials.{id}")
            ->addSelect("Partial inventory.{id,color,size}")
            ->addSelect("Partial product.{id,title,length,width,height,weight,mainImage}")
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    public function getPickerActivePickListByStorageBinSerial(string $binSerial, Admin $picker): array
    {
        return $this->createQueryBuilder('pickList')
                    ->innerJoin("pickList.storageBin", "storageBin")
                    ->where('pickList.picker = :picker')
                    ->andWhere('pickList.status = :status')
                    ->andWhere('storageBin.serial = :serial')
                    ->select("Partial pickList.{id,status,priority,quantity}")
                    ->setParameters([
                        "picker" => $picker,
                        'status' => PickListStatusDictionary::PICKING,
                        'serial' => $binSerial,
                    ])
                    ->getQuery()
                    ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
                    ->getResult();
    }

    public function getAllReceiptPickList(Receipt $receipt): array
    {
        return $this->getReceiptPickListsQuery($receipt)
                    ->select("Partial pickList.{id,status,priority,quantity}")
                    ->getQuery()
                    ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
                    ->getResult();
    }

    public function getReceiptPickListsCount(Receipt $receipt): ?int
    {
        return $this->getReceiptPickListsQuery($receipt)
                    ->select("SUM(pickList.quantity)")
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    private function getReceiptPickListsQuery(Receipt $receipt): QueryBuilder
    {
        return $this->createQueryBuilder('pickList')
                    ->innerJoin("pickList.receiptItem", "receiptItem")
                    ->where('receiptItem.receipt = :receipt')
                    ->setParameter('receipt', $receipt);
    }

    public function findPickListsForInventoryWithNotCloseStatus($inventory)
    {
        return $this->createQueryBuilder('pickList')
                    ->innerJoin('pickList.receiptItem', 'receiptItem')
                    ->where('receiptItem.inventory = :inventory')
                    ->andWhere('pickList.status <> :status')
                    ->setParameters([
                        'inventory' => $inventory,
                        'status'    => PickListStatusDictionary::CLOSE,
                    ])
                    ->getQuery()
                    ->getResult();
    }

    public function getReserveStocksCountForInventoryInSpecificBin(
        Inventory $inventory,
        WarehouseStorageBin $storageBin
    ): ?int {
        return $this->createQueryBuilder('pickList')
                    ->innerJoin('pickList.receiptItem', 'receiptItem')
                    ->where('receiptItem.inventory = :inventory')
                    ->andWhere('pickList.status <> :status')
                    ->andWhere('pickList.storageBin = :storageBin')
                    ->setParameters([
                        'inventory'  => $inventory,
                        'storageBin' => $storageBin,
                        'status'     => PickListStatusDictionary::CLOSE,
                    ])
                    ->select('SUM(pickList.quantity)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function getStorageBinReserveStocksCount(WarehouseStorageBin $storageBin): ?int
    {
        return $this->createQueryBuilder('pickList')
                    ->where('pickList.status <> :status')
                    ->andWhere('pickList.storageBin = :storageBin')
                    ->setParameters([
                        'storageBin' => $storageBin,
                        'status'     => PickListStatusDictionary::CLOSE,
                    ])
                    ->select('SUM(pickList.quantity)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }
}
