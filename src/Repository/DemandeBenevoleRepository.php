<?php

namespace App\Repository;

use App\Entity\DemandeBenevole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeBenevole>
 *
 * @method DemandeBenevole|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeBenevole|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeBenevole[]    findAll()
 * @method DemandeBenevole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeBenevoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeBenevole::class);
    }

//    /**
//     * @return DemandeBenevole[] Returns an array of DemandeBenevole objects
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

//    public function findOneBySomeField($value): ?DemandeBenevole
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
