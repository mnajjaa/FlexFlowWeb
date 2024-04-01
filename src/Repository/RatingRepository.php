<?php

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Cours;

/**
 * @extends ServiceEntityRepository<Rating>
 *
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[]    findAll()
 * @method Rating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }


    public function getTotalLikes(string $nomCour): int
    {
        $entityManager = $this->getEntityManager();
        
        $query = $entityManager->createQuery(
            'SELECT COUNT(r)
            FROM App\Entity\Rating r
            WHERE r.nom_cour = :nomCour AND r.liked = true'
        )->setParameter('nomCour', $nomCour);
        
        return (int) $query->getSingleScalarResult();
    }
    
    public function getTotalDislikes(string $nomCour): int
    {
        $entityManager = $this->getEntityManager();
        
        $query = $entityManager->createQuery(
            'SELECT COUNT(r)
            FROM App\Entity\Rating r
            WHERE r.nom_cour = :nomCour AND r.disliked = true'
        )->setParameter('nomCour', $nomCour);
        
        return (int) $query->getSingleScalarResult();
    }

    public function getMostLikedCours(): ?array
    {
        $entityManager = $this->getEntityManager();
    
        $query = $entityManager->createQuery(
            'SELECT r.nom_cour, COUNT(r) as totalLikes
            FROM App\Entity\Rating r
            WHERE r.liked = true
            GROUP BY r.nom_cour
            ORDER BY totalLikes DESC'
        )->setMaxResults(1);
    
        return $query->getOneOrNullResult();
    }

    public function getMostHatedCours(): ?array
    {
        $entityManager = $this->getEntityManager();
    
        $query = $entityManager->createQuery(
            'SELECT r.nom_cour, COUNT(r) as totaldisLikes
            FROM App\Entity\Rating r
            WHERE r.disliked = true
            GROUP BY r.nom_cour
            ORDER BY totaldisLikes DESC'
        )->setMaxResults(1);
    
        return $query->getOneOrNullResult();
    }
    

    

//    /**
//     * @return Rating[] Returns an array of Rating objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Rating
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
