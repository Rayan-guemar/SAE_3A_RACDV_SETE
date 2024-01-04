<?php

namespace App\Repository;

use App\Entity\DemandeValidation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeValidation>
 *
 * @method DemandeValidation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeValidation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeValidation[]    findAll()
 * @method DemandeValidation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeValidationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeValidation::class);
    }

//    /**
//     * @return DemandeValidation[] Returns an array of DemandeValidation objects
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

//    public function findOneBySomeField($value): ?DemandeValidation
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
