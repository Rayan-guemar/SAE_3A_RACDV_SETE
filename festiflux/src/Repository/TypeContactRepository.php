<?php

namespace App\Repository;

use App\Entity\TypeContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeContact>
 *
 * @method TypeContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeContact[]    findAll()
 * @method TypeContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeContact::class);
    }

//    /**
//     * @return TypeContact[] Returns an array of TypeContact objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypeContact
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
