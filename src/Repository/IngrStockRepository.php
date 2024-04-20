<?php

namespace App\Repository;

use App\Entity\IngrStock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IngrStock>
 *
 * @method IngrStock|null find($id, $lockMode = null, $lockVersion = null)
 * @method IngrStock|null findOneBy(array $criteria, array $orderBy = null)
 * @method IngrStock[]    findAll()
 * @method IngrStock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngrStockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IngrStock::class);
    }

//    /**
//     * @return IngrStock[] Returns an array of IngrStock objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IngrStock
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
