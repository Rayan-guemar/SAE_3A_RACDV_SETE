<?php

namespace App\Repository;

use App\Entity\Plage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plage>
 *
 * @method Plage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plage[]    findAll()
 * @method Plage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plage::class);
    }

//    /**
//     * @return Plage[] Returns an array of Plage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Plage
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
