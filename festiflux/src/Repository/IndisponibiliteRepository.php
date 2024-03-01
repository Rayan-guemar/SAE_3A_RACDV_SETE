<?php

namespace App\Repository;

use App\Entity\Festival;
use App\Entity\Indisponibilite;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Indisponibilite>
 *
 * @method Indisponibilite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Indisponibilite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Indisponibilite[]    findAll()
 * @method Indisponibilite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndisponibiliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Indisponibilite::class);
    }

//    /**
//     * @return Indisponibilite[] Returns an array of Indisponibilite objects
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

//    public function findOneBySomeField($value): ?Indisponibilite
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function areIndisposDoneByUserAndFestival(Utilisateur $user, Festival $festival): bool {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->andWhere('i.utilisateur = :utilisateur')
            ->andWhere('i.festival = :festival')
            ->setParameter('utilisateur', $user)
            ->setParameter('festival', $festival)
            ->getQuery()
            ->getOneOrNullResult()[1] > 0;
            
            
    }
}
