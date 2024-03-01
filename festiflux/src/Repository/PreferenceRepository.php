<?php

namespace App\Repository;

use App\Entity\Festival;
use App\Entity\Preference;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Preference>
 *
 * @method Preference|null find($id, $lockMode = null, $lockVersion = null)
 * @method Preference|null findOneBy(array $criteria, array $orderBy = null)
 * @method Preference[]    findAll()
 * @method Preference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PreferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Preference::class);
    }

//    /**
//     * @return Preference[] Returns an array of Preference objects
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

//    public function findOneBySomeField($value): ?Preference
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

// SELECT COUNT(p.id)
// FROM preference p
// JOIN Poste p ON p.poste_id = p.id
// JOIN Festival f ON p.festival_id = f.id
// WHERE p.utilisateur_id = :utilisateur_id
// AND f.id = :festival_id;

    public function arePreferencesDoneByUserAndFestival(Utilisateur $u, Festival $f): bool{
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->join('p.poste', 'po')
            ->join('po.festival', 'f')
            ->where('p.utilisateur = :userId')
            ->andWhere('f.id = :festivalId')
            ->andWhere('p.degree <> 0')
            ->setParameter('userId', $u->getId())
            ->setParameter('festivalId', $f->getId())
            ->getQuery()
            ->getOneOrNullResult()[1] > 0;


    }

}
