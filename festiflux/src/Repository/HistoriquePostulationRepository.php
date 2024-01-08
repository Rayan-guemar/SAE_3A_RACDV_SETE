<?php

namespace App\Repository;

use App\Entity\HistoriquePostulation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoriquePostulation>
 *
 * @method HistoriquePostulation|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriquePostulation|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriquePostulation[]    findAll()
 * @method HistoriquePostulation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriquePostulationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriquePostulation::class);
    }

//    /**
//     * @return HistoriquePostulation[] Returns an array of HistoriquePostulation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HistoriquePostulation
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
