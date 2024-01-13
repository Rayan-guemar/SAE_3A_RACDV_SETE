<?php

namespace App\Repository;

use App\Entity\Postulations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Postulations>
 *
 * @method Postulations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Postulations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Postulations[]    findAll()
 * @method Postulations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostulationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Postulations::class);
    }

//    /**
//     * @return Postulations[] Returns an array of Postulations objects
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

//    public function findOneBySomeField($value): ?Postulations
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
