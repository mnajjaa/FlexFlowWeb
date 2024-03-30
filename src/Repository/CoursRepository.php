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


  // Méthode pour trouver des cours aléatoires par catégorie
  public function findRandomCoursByCategory($categorie, $limit, $excludedId = null)
{
    // Obtenez tous les IDs des cours dans la catégorie spécifiée
    $idsQueryBuilder = $this->createQueryBuilder('c')
        ->select('c.id')
        ->where('c.Categorie = :Categorie')
        ->setParameter('Categorie', $categorie);

    if ($excludedId !== null) {
        $idsQueryBuilder->andWhere('c.id != :excludedId')
            ->setParameter('excludedId', $excludedId);
    }

    // Exécutez la requête pour obtenir les IDs des cours
    $ids = array_column($idsQueryBuilder->getQuery()->getResult(), 'id');

    // Mélangez les IDs pour une sélection aléatoire
    shuffle($ids);

    // Sélectionnez les premiers IDs en fonction de la limite spécifiée
    $selectedIds = array_slice($ids, 0, $limit);

    // Construisez une requête pour obtenir les objets Cours correspondants
    $coursQueryBuilder = $this->createQueryBuilder('c2')
        ->where('c2.id IN (:selectedIds)')
        ->setParameter('selectedIds', $selectedIds);

    return $coursQueryBuilder->getQuery()->getResult();
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
