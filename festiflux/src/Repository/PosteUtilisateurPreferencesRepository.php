<?php

namespace App\Repository;

use App\Entity\PosteUtilisateurPreferences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PosteUtilisateurPreferences>
 *
 * @method PosteUtilisateurPreferences|null find($id, $lockMode = null, $lockVersion = null)
 * @method PosteUtilisateurPreferences|null findOneBy(array $criteria, array $orderBy = null)
 * @method PosteUtilisateurPreferences[]    findAll()
 * @method PosteUtilisateurPreferences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PosteUtilisateurPreferencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PosteUtilisateurPreferences::class);
    }

//    /**
//     * @return PosteUtilisateurPreferences[] Returns an array of PosteUtilisateurPreferences objects
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

//    public function findOneBySomeField($value): ?PosteUtilisateurPreferences
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
