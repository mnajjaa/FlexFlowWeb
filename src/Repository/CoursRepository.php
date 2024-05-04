<?php

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cours>
 *
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }


    public function findDistinctCategories(): array
    {
        $queryBuilder = $this->createQueryBuilder('cours')
            ->select('DISTINCT cours.Categorie AS categorie')
            ->getQuery();

        $result = $queryBuilder->getResult();

        $categories = array_map(function($item) {
            return $item['categorie'];
        }, $result);

        return $categories;
    }

    public function findDistinctObjectifs(): array
{
    $queryBuilder = $this->createQueryBuilder('cours')
        ->select('DISTINCT cours.Objectif AS objectif')
        ->getQuery();

    $result = $queryBuilder->getResult();

    $objectifs = array_map(function($item) {
        return $item['objectif'];
    }, $result);

    return $objectifs;
}

public function findDistinctCibles(): array
{
    $queryBuilder = $this->createQueryBuilder('cours')
        ->select('DISTINCT cours.Cible AS cible')
        ->getQuery();

    $result = $queryBuilder->getResult();

    $cibles = array_map(function($item) {
        return $item['cible'];
    }, $result);

    return $cibles;
}

//    /**
//     * @return Cours[] Returns an array of Cours objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Cours
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
