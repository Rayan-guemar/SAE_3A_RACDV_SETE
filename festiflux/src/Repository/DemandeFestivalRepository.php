<?php

namespace App\Repository;

use App\Entity\DemandeFestival;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeFestival>
 *
 * @method DemandeFestival|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeFestival|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeFestival[]    findAll()
 * @method DemandeFestival[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeFestivalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeFestival::class);
    }

//    /**
//     * @return DemandeFestival[] Returns an array of DemandeFestival objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DemandeFestival
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
