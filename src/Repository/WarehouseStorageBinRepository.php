<?php

namespace App\Repository;

use App\Entity\WarehouseStorageBin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WarehouseStorageBin|null find($id, $lockMode = null, $lockVersion = null)
 * @method WarehouseStorageBin|null findOneBy(array $criteria, array $orderBy = null)
 * @method WarehouseStorageBin[]    findAll()
 * @method WarehouseStorageBin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarehouseStorageBinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarehouseStorageBin::class);
    }

    public function getBinWithSerialsAndType(array $serials, string $type): array
    {
        $qb = $this->createQueryBuilder('wsb')
                   ->leftJoin('wsb.children', 'children');

        return $qb->andWhere($qb->expr()->in('wsb.serial', ':serials'))
                  ->andWhere($qb->expr()->eq('wsb.type', ':type'))
                  ->setParameters([
                      'serials' => $serials,
                      'type'    => $type,
                  ])
                  ->getQuery()
                  ->getResult();
    }

    public function getSerials(array $serials): array
    {
        $qb     = $this->createQueryBuilder('wsb');
        $result = $qb->where($qb->expr()->in('wsb.serial', ':serials'))
                     ->select('wsb.serial')
                     ->setParameter('serials', $serials)
                     ->getQuery()
                     ->getResult();

        return array_column($result, 'serial');
    }
}
