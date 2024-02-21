<?php

namespace App\Repository;

use App\Entity\Festival;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Festival>
 *
 * @method Festival|null find($id, $lockMode = null, $lockVersion = null)
 * @method Festival|null findOneBy(array $criteria, array $orderBy = null)
 * @method Festival[]    findAll()
 * @method Festival[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FestivalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Festival::class);
    }

//    /**
//     * @return Festival[] Returns an array of Festival objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Festival
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    /**
     * Get published fests thanks to Search Data value
     *
     * @param SearchData $searchData
     * @return array
     */
    public function findBySearch(SearchData $searchData): array
    {
        $data = $this->createQueryBuilder('p');

        if (!empty($searchData->q)) {
            $data = $data
                ->Where('p.nom LIKE :searchTerm OR p.lieu LIKE :searchTerm')
                ->setParameter('searchTerm', "%{$searchData->q}%");
        }

        $data = $data
            ->getQuery()
            ->getResult();

        return $data;
    }


}
