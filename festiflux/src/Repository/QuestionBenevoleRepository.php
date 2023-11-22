<?php

namespace App\Repository;

use App\Entity\QuestionBenevole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionBenevole>
 *
 * @method QuestionBenevole|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionBenevole|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionBenevole[]    findAll()
 * @method QuestionBenevole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionBenevoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionBenevole::class);
    }

//    /**
//     * @return QuestionBenevole[] Returns an array of QuestionBenevole objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?QuestionBenevole
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
